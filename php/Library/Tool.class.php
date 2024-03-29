<?php
/**
 * Created by PhpStorm.
 * User: fangd
 * Date: 2019/1/4 004
 * Time: 12:01
 */

namespace Library;

class Tool
{
	const DATE_FORMAT     = 'Y-m-d';
	const TIME_FORMAT     = 'H:i:s';
	const DATETIME_FORMAT = 'Y-m-d H:i:s';
	const EMAIL_REGULAR    = '/^[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}$/i';
	const MOBILE_REGULAR   = '/^[1][0-9]{10}$/';
	const FIXED_REGULAR    = '/^[A-Za-z0-9]{6,30}+$/';
	const PASSWORD_REGULAR = '/^[A-Za-z0-9]{6,30}+$/';

    /**
     * emoji标签编码
     * @param array $str
     * @return array
     */
    public function userTextEncode($str){
        if(!is_string($str)) return $str;
        if(!$str || $str=='undefined') return '';
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback("/(\\\u[2def][0-9a-f]{3})/i",function($str){
            return addslashes($str[0]);
        },$text); //将emoji的unicode留下，其他不动，这里的正则比原答案增加了d，因为我发现我很多emoji实际上是\ud开头的，反而暂时没发现有\ue开头。
        return json_decode($text);
    }

    /**
     * emoji标签反编码
     * @param array $str
     * @return array
     */
    public function userTextDecode($str)
    {
        $text = json_encode($str); //暴露出unicode
        $text = preg_replace_callback('/\\\\\\\\/i', function ($str) {
            return '\\';
        }, $text); //将两条斜杠变成一条，其他不动
        return json_decode($text);
    }

    /**
     * 快速日志
     * @param array $data
     * @param string $project
     * @param string $func
     * @return array
     */
    public function quickLog($data,$project,$func){
        file_put_contents('/data/logs/'.$project.'/'.date('Y-m-d').'.log', $func.': ' . date('Y-m-d H:i:s')." - data: ".json_encode($data)."\r\n", FILE_APPEND);
    }

    /**
     * 搜寻指定参数并处理
     * @param $data [array] 源数组
     * @param $time [int] key
     * @param $func [function] 匿名函数，用来处理搜索到的值
     * @param $format [string] sprintf的format参数
     * @param $index [int] 默认0，递归使用
     * @param $isArray [bool] 默认false，递归使用
     * @example deepTranslate($data,[
     *  'item','0','name'
     * ],function ($d){ return $d+1; }) //0代表下一级是数组
     * @return array
     */
    public function deepTranslate($data,$keys,$func=null,$format='%sEn',$index=0,$isArray=false){
        $key = $keys[$index];
        $index++;
        if($key == '1'){
            $data = self::deepTranslate($data,$keys,$func,$format,$index,true);
        }else{
            if(isset($data[$key]) && is_array($data[$key]) && !$isArray){
                $data[$key] = self::deepTranslate($data[$key],$keys,$func,$format,$index);
            }else if($isArray){
                foreach($data as $k=>$v){
                    $data[$k][sprintf($format,$key)] = $func($v[$key]);
                }
            }else{
                $data[sprintf($format,$key)] = $func($data[$key]);
                return $data;
            }
        }
        return $data;
    }

    /**
     * 格式化retryTask格式
     * @param $func [func] 重试匿名函数
     * @param $data [array] 匿名函数传入参数
     * @param $time [int] 重试次数
     * @param $result [array] 返回结果
     * @param $sleepMs [int] 间隔时间，单位为毫秒
     * @return array
     */
    public static function retryTask($func,$data,$time=3,&$result,$sleepMs=0){
        if($time == 0){
            return ;
        }
        $r = $func($data);
        $result[] = $r;
        sleep($sleepMs/1000);
        if(!$r['status']){
            $time--;
            self::retryTask($func,$data,$time,$result);
        }
    }

    /**
     * 格式化key-value格式
     * @param $key
     * @param $value
     * @param $source
     * @return array
     */
    public static function keyValueFilter($key,$value,$source){
        $response = [];
        foreach($source as $item){
            $response[$item[$key]] = $item[$value];
        }
        return $response;
    }

    /**
     * 高德坐标转换为百度坐标
     * @param  [type] $lng 经度
     * @param  [type] $lat 纬度
     * @param  [type] $toType 转换类型，bdTogd=百度转高德，gdTobd=高德转百度
     * @return array      [description]
     */
    public static function coorsConvert($log, $lat, $toType) {
        if (!settype($log, 'float') || !settype($lat, 'float')) {
            return array();
        } else {
            //判断经纬度的合法性
            if ($log < -180 || $log > 180 || $lat < -90 || $lat > 90) {
                return array();
            }
        }

        if ($toType == 'bdTogd') {//百度转高德
            $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
            $x = $log - 0.0065;
            $y = $lat - 0.006;
            $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
            $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
            $data['lon'] = $z * cos($theta);
            $data['lat'] = $z * sin($theta);
            return $data;
        } else if ($toType == 'gdTobd') {//高德转百度
            $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
            $x = $log;
            $y = $lat;
            $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
            $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
            $data['lon'] = $z * cos($theta) + 0.0065;
            $data['lat'] = $z * sin($theta) + 0.006;
            return $data;
        } else {
            $data['lon'] = $log;
            $data['lat'] = $lat;
            return $data;
        }
    }

    /**
     * 自定义array_column
     * @param $source
     * @param $column
     * @return array
     */
    public static function cusArrayColumn($source, $column){
        return array_values(array_filter(array_unique(array_column($source,$column))));
    }

    /**
     * 多级数组组合
     * @param $source
     * @example combineArray([
        [[[
        'aa' => '11',
        'bb' => '22'
        ]],'bb'],
        [[[
        'bb' => '22',
        'cc' => '33'
        ]],'cc'],
        [[[
        'cc' => '33',
        'dd' => '44'
        ]],'dd']
        ]);
     * [[数组,主键及下面数组的关系字段]]
     * @return mixed
     */
    public static function combineArray($source){
        $data = [];
        foreach($source as $index=>$item){
            foreach($item[0] as $v){
                $data[$index][$v[$item[1]]] = $v;
            }
        }
        $len = count($data);
        for($i=0;$i<$len-1;$i++){
            $data[$len-$i-2] = self::combine([$data[$len-$i-2],$data[$len-$i-1]],$source[$len-$i-2][1]);
        }
        return $data[0];
    }

    public static function combine($data,$relation){
        foreach($data[1] as $k => $item){
            $data[0][
            $item[$relation]
            ][$relation.'Child'][$k] = $item;
        }
        return $data[0];
    }

    /**
     * 判断是否存在或是否为空，如果为空，不声明变量
     * @param $source 数据源
     * @param $key 数组key值
     * @param $output 数组
     */
    public static function checkEmpty($source, $key, &$output){
        if(isset($source) && $source){
            $output[$key] = $source;
        }
    }

    /**
     * 自定义合并数组
     * @param $source
     * @param int $isSaveKey 1为保留键名，0为不保留，相同键名会合并
     * @param string 数组键名
     * @param array 数组
     * @return array
     */
    public static function arrayMerge($source, $isSaveKey=0,$key='',&$output){
        $response = [];
        foreach($source as $item){
            if($item){
                foreach($item as $key=>$value){
                    if($isSaveKey==1){
                        $response[$key] = $value;
                    }else{
                        $response[] = $value;
                    }
                }
            }
        }

        $response = ($isSaveKey==1)?array_unique($response):array_values(array_unique($response));
        if($key != ''){
            $output[$key] = $response;
        }
        return $response;
    }

    /**
     * 格式化key-value格式
     * @param $key
     * @param $data
     * @return array
     */
    public static function keyValue($key, $data){
	    $response = [];
	    foreach($data as $item){
            $response[$item[$key]] = $item;
        }
	    return $response;
    }

    /**
     * 判断空及设置默认值
     * @param $sourceAndDefault
     * @return array [值,默认值,过滤函数]
     * @example $datas = checkEmptyAndSet([
                    'title'   => [$ret['title'],'','trim'],
                    'list'    => [$ret['list'],'','strval'],
                    'last_id' => [$ret['last_id'],null],
                    'count'   => [$ret['count'],0],
                ]);
     */
    public static function checkEmptyAndSet($sourceAndDefault){
        $response = [];
	    foreach($sourceAndDefault as $key => $value){
	        if(isset($value[2])){
                $response[$key] = isset($value[0]) && $value[2]($value[0])?$value[2]($value[0]):$value[1];
            }else{
                $response[$key] = isset($value[0]) && $value[0]?$value[0]:$value[1];
            }
        }
	    return $response;
    }

    /**
     * 根据参数过来数组元素
     * @param $filter
     * @param array $data
     * @return array
     */
    public static function filterFields($filter, $data = array())
    {
        $new_fields = array();
        $data = array_filter($data); //过滤空值
        foreach ($data as $key=>$item) {
            if(in_array($key, $filter) && is_string($item)){
                $new_fields[$key] = addslashes($item);
            }else{
                $new_fields[$key] = $item;
            }
        }
        return $new_fields;
    }

    /**
     * 是否在设定时间段内
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    public static function isInPeriodTime($startTime, $endTime){
	    return (strtotime($startTime) <=time() && strtotime($endTime) >=time())?true:false;
    }

    /**
     * 根据指定键名排序
     * @param $data array 数据源
     * @param $key array 排序字段
     * @return array
     */
    public static function sortBykeys($data, $key){
        $response = [];
        foreach($key as $v){
            $response[$v] = $data[$v];
        }
        return $response;
    }

    /**
     * 判断是否是周末
     * @param int $date
     * @return bool
     */
    public static function isWeekend($date=0){
	    if($date){
            if(in_array(date('w',strtotime($date)),[6,7])){
                return true;
            }
        }else{
            if(in_array(date('w'),[6,7])){
                return true;
            }
        }
	    return false;
    }

	public static function date($input, $format = null)
	{
		if ($input instanceof \DateTimeInterface
		    || $input instanceof \DateTime) {
			return true;
		}

		if (!is_scalar($input)) {
			return false;
		}

		$inputString = (string)$input;

		if ($format === null) {
			return false !== strtotime($inputString);
		}

		$exceptionalFormats = [
			'c' => 'Y-m-d\TH:i:sP',
			'r' => 'D, d M Y H:i:s O',
		];

		if (in_array($format, array_keys($exceptionalFormats))) {
			$format = $exceptionalFormats[$format];
		}

		$info = date_parse_from_format($format, $inputString);

		return ($info['error_count'] === 0 && $info['warning_count'] === 0);
	}

	/**
	 * 移动电话号码格式
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public static function mobile($input)
	{
		return preg_match(self::MOBILE_REGULAR, $input) === 1;
	}

	/**
	 * 验证固定电话号码格式
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public static function fixed($input)
	{
		return preg_match(self::FIXED_REGULAR, $input) === 1;
	}

	/**
	 * 验证电子邮件地址格式
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public static function email($input)
	{
		return preg_match(self::EMAIL_REGULAR, $input) === 1;
	}

	/**
	 * 验证密码格式
	 *
	 * @param $input
	 *
	 * @return bool
	 */
	public static function password($input)
	{
		return preg_match(self::PASSWORD_REGULAR, $input) === 1;
	}


	/**
	 * @param array $array
	 *
	 * @return array
	 */
	public static function removeNull(array $array)
	{
		foreach ($array as $index => $item) {
			if ($item === null) {
				unset($array[$index]);
			}
		}

		return $array;
	}

	public static function removeEmpty(array $array)
	{
		foreach ($array as $index => $item) {
			if (empty($array)) {
				unset($array[$index]);
			}
		}

		return $array;
	}

	public static function removeNonInt(array $array)
	{
		foreach ($array as $index => $item) {
			if ($item != (int)$item) {
				unset($array[$index]);
			} else {
				$array[$index] = (int)$item;
			}
		}

		return $array;
	}

	public static function removeNonNumeric(array $array)
	{
		foreach ($array as $index => $item) {
			if (!is_numeric($item)) {
				unset($array[$index]);
			}
		}

		return $array;
	}

	public static function removeNonFloat(array $array)
	{
		foreach ($array as $index => $item) {
			if (!is_float($item)) {
				unset($array[$index]);
			}
		}

		return $array;
	}

	public static function toStringArray(array $array)
	{
		foreach ($array as $index => $item) {
			$array[$index] = (string)$item;
		}
		return $array;
	}

	public static function toInt($value)
	{
		return $value === null ? null : (int)$value;
	}

	public static function toFloat($value)
	{
		return $value === null ? null : (float)$value;
	}

	public static function toString($value)
	{
		return $value === null ? null : (string)$value;
	}


	/** @var \DateTimeZone */
	private static $timezone;

	/**
	 * @return \DateTimeZone
	 */
	public static function getTimezone()
	{
		if (empty(self::$timezone)) {
			self::$timezone = new \DateTimeZone(date_default_timezone_get());
		}
		return self::$timezone;
	}

	public static function toUnixTimestamp($value)
	{
		if (is_int($value)) {
			return $value;
		}

		if (is_string($value)) {
			return strtotime($value);
		}

		if ($value instanceof \MongoDate) {
			$value = self::toDateTime($value);
		}

		if ($value instanceof \DateTime) {
			return $value->getTimestamp();
		}

		return false;
	}

	public static function format($value, $format = self::DATETIME_FORMAT, $useEmpty = false)
	{
		if (is_string($value)) {
//			$value = strtotime($value);
			return $value;
		}

		if ($value instanceof \MongoDate) {
			$value = self::toDateTime($value);
		}

		if ($value instanceof \DateTime) {
			if ($useEmpty && $value->getTimestamp() == 0) {
				return '';
			}
			return $value->format($format);
		}

		if (is_int($value)) {
			if ($useEmpty && $value == 0) {
				return '';
			}
			return date($format, $value);
		}

		return false;
	}

	public static function toDateTime($value)
	{
		if (is_string($value)) {
			return new \DateTime($value);
		}

		if (is_int($value)) {
			$result = new \DateTime();
			$result->setTimestamp($value);
			return $result;
		}

		if ($value instanceof \MongoDate) {
			$value = $value->toDateTime();
			$value->setTimezone(self::getTimezone());
			return $value;
		}

		return false;
	}

	public static function toMongoDate($value)
	{
		if ($value instanceof \MongoDate) {
			return $value;
		}

		$timestamp = self::toUnixTimestamp($value);
		if ($timestamp < 0) {
			$timestamp = 0;
		}

		return $timestamp === false ? false : new \MongoDate($timestamp);
	}

	private static $profiles = [];

	public static function start($key)
	{
		self::$profiles[$key] = microtime(true);
	}

	public static function end($key)
	{
		return self::$profiles[$key] = round(microtime(true) - self::$profiles[$key], 4);
	}

	/**
	 * @return array
	 */
	public static function getProfiles()
	{
		return self::$profiles;
	}


	/**
	 * @var \ReflectionClass[]
	 */
	private static $reflections = [];
	private static $methods     = [];
	/** @var string[][] */
	private static $methodNames = [];

	/**
	 * @param string | object $classOrObject
	 *
	 * @return \ReflectionClass
	 * @throws \ReflectionException
	 */
	public static function getReflection($classOrObject)
	{
		if (is_object($classOrObject)) {
			return new \ReflectionObject($classOrObject);
		}

		if (empty(self::$reflections[$classOrObject])) {
			self::$reflections[$classOrObject] = new \ReflectionClass($classOrObject);
		}

		return self::$reflections[$classOrObject];
	}

	/**
	 * @param string | object $classOrObject
	 *
	 * @param null            $filter
	 *
	 * @return \ReflectionProperty[]
	 * @throws \ReflectionException
	 */
	public static function getProperties($classOrObject, $filter = null)
	{
		$reflection = self::getReflection($classOrObject);
		$properties = [];

		if ($filter === null) {
			$list = $reflection->getProperties();
		} else {
			$list = $reflection->getProperties($filter);
		}

		foreach ($list as $property) {
			$properties[$property->name] = $property;
		}

		if ($parent = $reflection->getParentClass()) {
			$parentProperties = self::getProperties($parent->getName(), $filter);
			$properties       = array_merge($parentProperties, $properties);
		}

		return $properties;
	}

	/**
	 * @param $classOrObject
	 * @param $name
	 *
	 * @return \ReflectionProperty
	 * @throws \ReflectionException
	 */
	public static function getProperty($classOrObject, $name)
	{
		$reflection = self::getReflection($classOrObject);
		return $reflection->getProperty($name);
	}

	/**
	 * @param string | object $classOrObject
	 * @param null            $filter
	 *
	 * @return \ReflectionMethod[]
	 * @throws \ReflectionException
	 */
	public static function getMethods($classOrObject, $filter = null)
	{
		$reflection = self::getReflection($classOrObject);
		$className  = $reflection->getName();
		if (isset(self::$methods[$className][$filter])) {
			return self::$methods[$className][$filter];
		}

		self::$methods[$className] = [];

		if ($filter === null) {
			$list = $reflection->getMethods();
		} else {
			$list = $reflection->getMethods($filter);
		}

		foreach ($list as $method) {
			self::$methods[$className][$filter][$method->getName()] = $method;
		}

		return self::$methods[$className][$filter];
	}

	/**
	 * @param $classOrObject
	 * @param $name
	 *
	 * @return \ReflectionMethod
	 * @throws \ReflectionException
	 */
	public static function getMethod($classOrObject, $name)
	{
		$reflection = self::getReflection($classOrObject);
		if (isset(self::$methods[$reflection->getName()][$name])) {
			return self::$methods[$reflection->getName()][$name];
		}

		return $reflection->getMethod($name);
	}

	/**
	 * @param $class
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	public static function getPublicObjectMethodNames($class)
	{
		if (is_object($class)) {
			$class = get_class($class);
		}

		if (isset(self::$methodNames[$class])) {
			return self::$methodNames[$class];
		}

		$methods = self::getMethods($class, \ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $name => $method) {
			if (!$method->isStatic()) {
				self::$methodNames[$class][] = $name;
			}
		}

		return self::$methodNames[$class];
	}


	/**
	 * Converts a word into the format for a Doctrine table name. Converts 'ModelName' to 'model_name'.
	 *
	 * @param $word
	 *
	 * @return string
	 */
	public static function tableize($word)
	{
		return strtolower(preg_replace('~(?<=\\w)([A-Z])~u', '_$1', $word));
	}

	/**
	 * Converts a word into the format for a Doctrine class name. Converts 'table_name' to 'TableName'.
	 *
	 * @param $word
	 *
	 * @return string
	 */
	public static function classify($word)
	{
		return str_replace([' ', '_', '-'], '', ucwords($word, ' _-'));
	}

	/**
	 * Camelizes a word. This uses the classify() method and turns the first character to lowercase.
	 *
	 * @param $word
	 *
	 * @return string
	 */
	public static function camelize($word)
	{
		return lcfirst(self::classify($word));
	}

	/**
	 * Uppercases words with configurable delimiters between words.
	 *
	 * Takes a string and capitalizes all of the words, like PHP's built-in
	 * ucwords function. This extends that behavior, however, by allowing the
	 * word delimiters to be configured, rather than only separating on
	 * whitespace.
	 *
	 * Here is an example:
	 * <code>
	 * <?php
	 * $string = 'top-o-the-morning to all_of_you!';
	 * echo $inflector->capitalize($string);
	 * // Top-O-The-Morning To All_of_you!
	 *
	 * echo $inflector->capitalize($string, '-_ ');
	 * // Top-O-The-Morning To All_Of_You!
	 * ?>
	 * </code>
	 *
	 * @param string $string     The string to operate on.
	 * @param string $delimiters A list of word separators.
	 *
	 * @return string The string with all delimiter-separated words capitalized.
	 */
	public static function capitalize($string, $delimiters = " \n\t\r\0\x0B-")
	{
		return ucwords($string, $delimiters);
	}


	public static function randomString($length = 16, $charList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?')
	{
		$i      = 0;
		$random = "";
		while ($i < $length) {
			$random .= $charList{mt_rand(0, (strlen($charList) - 1))};
			$i++;
		}
		return $random;
	}

	public static function templateReplace($string, array $data, $tag = '{{$%s}}')
	{
		foreach ($data as $key => $value) {
			$string = str_replace(sprintf($tag, $key), $value, $string);
		}

		return $string;
	}

    /**
     *
     * @Author: fangd <fanguodong@daojia.com.cn>
     * @Date: 2019/12/26 11:24
     * @param null $name
     * @param null $default
     * @return array|mixed|null
     */
    public function getParams($name = null, $default = null)
    {
        if ($name === null) {
            return $_POST['info'];
        }

        if (is_string($name) && isset($_POST['info'][$name])) {
            return $_POST['info'][$name];
        }

        if (is_array($name)) {
            $params = [];
            if (is_array($default)) $params = $default;
            foreach ($name as $index => $item) {
                if (isset($_POST['info'][$item])) {
                    if (is_numeric($index)) {
                        $params[$item] = $_POST['info'][$item];
                    } else {
                        $params[$index] = $_POST['info'][$item];
                    }
                }
            }
            return $params;
        }

        return $default;
    }

    public static function isPNG($data)
    {
        return bin2hex(substr($data, 0, 8)) === '89504e470d0a1a0a';
    }

    public static function isJPG($data)
    {
        $code = bin2hex(substr($data, 0, 4));
        return in_array($code, ['ffd8ffe0', 'ffd8ffe1', 'ffd8ffe8']);
    }

    public static function isGIF($data)
    {
        $code = bin2hex(substr($data, 0, 6));
        return $code == '474946383961' || $code == '474946383961';
    }
}

