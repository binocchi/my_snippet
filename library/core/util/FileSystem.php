<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Mk\Core;

class FileSystem
{
	/**
	 * ディレクトリ作成
	 * @param unknown $directory
	 * @return boolean
	 */
	public static function createDirectory($directory)
	{
		if (is_dir($directory) === true) {
			return true;
		}

		if (mkdir($directory, 0775)) {
			return true;
		}

		return false;
	}

	/**
	 * @param $file
	 * @return bool
	 */
	public static function createFile($file)
	{
		if (file_exists($file) === true) {
			return true;
		}

		if (touch($fileName)) {
			return true;
		}

		return false;
	}

	/**
	 * ファイルへの書き込み
	 * @param unknown $fileName
	 * @param unknown $text
	 */
	public static function writeFile($fileName, $text)
	{
		$handler = fopen($fileName, 'w');
		flock($handler, LOCK_EX);
		fwrite($handler, $text);
		fclose($handler);
	}
}