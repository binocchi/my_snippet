<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Mk\Core;

class Logger
{
	private static $_runFileName = '';
	private static $_fileNames = array();
	private static $_logFormat = '[%time%],[%host%],[%filename%],[%level%],%message%';
	private static $_timeFormat = 'Y-m-d H:i:s';

	public static function setLogName($fileName, $logKey = 'default')
	{
		self::$_fileNames[$logKey] = $fileName;
	}

	public static function setDefaultLogName($addName = '', $logKey = 'default', $runFileName = '')
	{
		if (empty($addName)) {
			$fileName = DIR_LOG . Format::date() . $addName . '.log';
		} else {
			$fileName = DIR_LOG . $addName . '.log';
		}

		FileSystem::createDirectory(DIR_LOG);
		self::$_fileNames[$logKey] = $fileName;
		self::$_runFileName = $runFileName;
	}

	public static function getRunFileName()
	{
		return self::$_runFileName;
	}

	public static function setRunFileName($runFileName)
	{
		self::$_runFileName = $runFileName;
	}

	public static function getLogName($logKey = 'default')
	{
		return self::$_fileNames[$logKey];
	}

	public static function setLogFormat($format)
	{
		self::$_logFormat = $format;
	}

	public static function setTimeFormat($format)
	{
		self::$_timeFormat = $format;
	}

	public static function write($message, $logLevel = 'INFO', $logKey = 'default')
	{
		if (!array_key_exists($logKey, self::$_fileNames)) {
			return;
		}

		$filePath = self::$_fileNames[$logKey];
		if (file_exists($filePath) && is_file($filePath) && !is_writable($filePath)) {
			return;
		}

		$logText = self::$_logFormat;
		$logText = str_replace('%time%', date(self::$_timeFormat), $logText);
		$logText = str_replace('%host%', gethostname(), $logText);
		$logText = str_replace('%level%', $logLevel, $logText);
		$logText = str_replace('%filename%', self::$_runFileName, $logText);
		$logText = str_replace('%message%', (empty(self::$_groupName) ? $message : self::$_groupName . ',' . $message), $logText);
		$logText .= "\n";

		$r = @fopen($filePath, 'a');
		@fputs($r, $logText);
		@fclose($r);
	}

	public static function info($message, $logKey = 'default')
	{
		self::write($message, 'INFO', $logKey);
	}

	public static function warn($message, $logKey = 'default')
	{
		self::write($message, 'WARNING', $logKey);
	}

	public static function notice($message, $logKey = 'default')
	{
		self::write($message, 'NOTICE', $logKey);
	}

	public static function critical($message, $logKey = 'default')
	{
		self::write($message, 'CRITICAL', $logKey);
	}

	public static function error($message, $logKey = 'default')
	{
		self::write($message, 'ERROR', $logKey);
	}

	public static function debug($message, $logKey = 'default')
	{
		self::write($message, 'DEBUG', $logKey);
	}

	public static function except(Exception $e, $logKey = 'default', $type = '')
	{
		$message = sprintf(
			"FILE:%s LINE:%s %s\n%s",
			$e->getFile(),
			$e->getLine(),
			$e->getMessage(),
			$e->getTraceAsString()
		);

		switch ($type) {
			case 'INFO':
				self::info($message, $logKey);
				break;

			case 'WARNING':
				self::warn($message, $logKey);
				break;

			case 'NOTICE':
				self::notice($message, $logKey);
				break;

			case 'CRITICAL':
				self::notice($message, $logKey);
				break;

			default:
				self::error($message, $logKey);
				break;
		}
	}
}
