<?php
/**
 * Created by PhpStorm.
 * User: goo1016
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
     * 是否在设定时间段内
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    public static function isInPeriodTime($startTime, $endTime){
	    return (strtotime($startTime) <=time() && strtotime($endTime) >=time())?true:false;
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
}

