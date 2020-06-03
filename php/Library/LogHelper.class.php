<?php
/**
 * Created by PhpStorm.
 * User: goo1016
 * Date: 2018/7/20 020
 * Time: 16:41
 */

namespace Ucenter\Library;


use Ucenter\Library\Mapper\MapperHelper;

class LogHelper
{

	public static function write($level, $class, $method, $contents)
	{
		$datetime = date('Y-m-d H:i:s.') . floor(microtime() * 10000);
		$path     = C('DJ_SERVER_Module') . '/' . C('DJ_SERVER_Controller') . '/' . C('DJ_SERVER_Action');
		$content  = "[{$path}][{$level}][{$datetime}][{$class}][$method]";
		if (is_array($contents)) {
			$content .= '[' . json_encode($contents, JSON_UNESCAPED_UNICODE) . ']';
		} else {
			$content .= "[{$contents}]";
		}

		$filename = C('LOG_PATH') . date('Y-m-d') . '.log';

		if (!file_exists($filename)) {
			touch($filename);
		}

		file_put_contents($filename, "{$content}\n\n", FILE_APPEND);
	}

	public static function response($class, $method, $response, $code, $message = null)
	{
		self::write(
			'response',
			$class,
			$method,
			[
				'code'     => $code,
				'message'  => $message,
				'response' => $response,
				'request'  => $_POST['info'],
				'profiler' => Tool::getProfiles(),
			]
		);
	}

	public static function info($class, $method, $contents)
	{
		self::write('info', $class, $method, $contents);
	}

	public static function exception($class, $method, \Throwable $exception)
	{
		self::write('exception', $class, $method, self::formatException($exception));
	}

	public static function formatException(\Throwable $exception)
	{
		$message = '';

		if ($exception->getPrevious()) {
			$message .= self::formatException($exception->getPrevious());
		}

		$type    = get_class($exception);
		$message .= "TYPE:{$type}\n";
		$message .= "CODE:{$exception->getCode()}\n";
		$message .= "Message:{$exception->getMessage()}\n";
		$message .= "FILE:{$exception->getFile()}:{$exception->getLine()}\n\n";
		$message .= $exception->getTraceAsString();
		$message .= "\n\n";

		return $message;
	}

	/**
	 * @param \Throwable $exception
	 *
	 * @return array|null
	 * @throws \ReflectionException
	 */
	public static function exceptionToArray(\Throwable $exception)
	{
		if (empty($exception)) {
			return null;
		}

		$array             = MapperHelper::toArray($exception);
		$array['class']    = get_class($exception);
		$array['trace']    = explode("\n", $exception->getTraceAsString());
		$array['previous'] = $exception->getPrevious() ? self::exceptionToArray($exception->getPrevious()) : null;

		return $array;
	}

}

