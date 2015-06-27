<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Mk\Core;
/**
 * 機能一覧
 *  - _doLoadingYML($yml_name);
 *    confディレクトリ配下のYMLファイルをロードする
 *    ex ) /conf/test/hogehoge.ymlを読み込む場合
 *         _doLoadingYML('test/hogehoge');
 *
 *  - getValueAll($arg1, $arg2, ...);
 *  - getAllByArray($ymlName, array $args);
 *    指定ファイル, 指定階層のデータ取得
 *    ex ) /conf/test/hogehoge.yml の 階層 level1 > level2 配下のデータ取得
 *         getValueAll('test/hogehoge', 'level1', 'level2');
 *         getAllByArray('test/hogehoge', array('level1', 'level2'));
 *
 *  - getValueText($arg1, $arg2, ...);
 *    指定ファイル, 指定階層のデータ取得, 文字列置換可
 *    ex ) /conf/test/hogehoge.yml の 階層 level1 > level2 配下のデータ取得
 *         getValueText('test/hogehoge', 'level1', 'level2', array('置換文字列1', '置換文字列2', ....));
 *
 *  - getEnvironment();
 *    host毎の環境設定取得
 *
 *  - getActionYml($mode, $data);
 *    画面毎の設定取得
 */
class BaseYmlLoader
{
	protected static $yml_data = "";

	private static $yml_cache = array();
	private static $user_cache;
	private static $server_name;

	protected static function _doLoadingYML($yml_name)
	{
		$yml_name = DIR_CONFIG_YML . $yml_name . '.yml';

		if (!file_exists($yml_name)) {
			self::$yml_data = false;
			return;
		}

		// 環境変数取得
		self::$server_name = getenv('HOST_NAME') ? getenv('HOST_NAME') : 'localhost';

		if (!isset(self::$user_cache)) {
			// YMLからキャッシュを利用するかどうかを取得
			$env = \Spyc::YAMLLoad(DIR_CONFIG_YML . 'environment.yml');

			self::$user_cache = false;
			if (isset($env[self::$server_name]['yml']['use_cache'])) {
				self::$user_cache = $env[self::$server_name]['yml']['use_cache'];

			} else {
				if (isset($env['DEFAULT']['yml']['use_cache'])) {
					self::$user_cache = $env['DEFAULT']['yml']['use_cache'];
				}
			}

			//キャッシュを利用しない場合 キャッシュをクリア
			if (!self::$user_cache) {
				$iterator = new \DirectoryIterator(DIR_YML_CACHE);
				foreach ($iterator as $item) {
					if ($item->isFile()) {
						unlink($item->getPathName());
					}
				}
			}
		}

		// アプリケーションキャッシュ。
		if (!array_key_exists($yml_name, self::$yml_cache)) {
			$cache_filename = DIR_YML_CACHE . md5($yml_name);

			if (self::_isCached(DIR_YML_CACHE, $yml_name, $cache_filename)) {
				self::$yml_cache[$yml_name] = self::_getData(DIR_YML_CACHE, $yml_name, $cache_filename);
			} else {
				self::$yml_cache[$yml_name] = self::_store($yml_name, $cache_filename);
			}
		}
		self::$yml_data = self::$yml_cache[$yml_name];
	}


	private static function _getData($dir_cache, $filename, $cache_filename)
	{
		return self::_isCached($dir_cache, $filename, $cache_filename) ? self::_load($cache_filename) : self::_store($filename, $cache_filename);
	}

	private static function _isCached($dir_cache, $filename, $cache_filename)
	{
		if (!self::$user_cache) {
			return false;
		}
		clearstatcache();
		return file_exists($cache_filename) && (filemtime($filename) <= filemtime($cache_filename));
	}

	private static function _load($cache_filename)
	{
		return unserialize(file_get_contents($cache_filename));
	}

	private static function _store($filename, $cache_filename)
	{
		$data = \Spyc::YAMLLoad($filename);
		if (self::$user_cache) {
			if (!file_put_contents($cache_filename, serialize($data))) {
				throw new Exception('cache yml書き込み失敗');
			}
		}
		return $data;
	}

	/**
	 * データ取得
	 * [引数一覧]
	 * 1.string YML名
	 * 2.string YML階層(複数指定可)
	 */
	public static function getValueAll()
	{
		$args = func_get_args();
		$ymlName = array_shift($args);

		if (is_null($ymlName)) {
			return false;
		}

		self::_doLoadingYML($ymlName);

		$result = self::_searchYmlData($args);

		if (is_null($result)) {
			return false;
		}
		return $result;
	}

	/**
	 * getValueAllのラッパー
	 * [引数一覧]
	 * 1.string YML名
	 * 2.arrray YML階層
	 */
	public static function getAllByArray($ymlName, $args)
	{
		array_unshift($args, $ymlName);
		return call_user_func_array('self::getValueAll', $args);
	}

	/**
	 * データ取得(文字列のみ)
	 * 文字列置換可
	 * [引数一覧]
	 * 1.string YML名
	 * 2.string YML階層(複数指定可)
	 * 3.array  置換用データ配列(未指定可)
	 */
	public static function getValueText()
	{
		$args = func_get_args();

		$ymlName = array_shift($args);
		if (is_null($ymlName)) {
			return false;
		}

		self::_doLoadingYML($ymlName);

		$bind = array();
		if (is_array(end($args))) {
			$bind = array_pop($args);
		}

		$ymlData = self::_searchYmlData($args);
		if (is_null($ymlData) || is_array($ymlData)) {
			return false;
		}

		return (string)self::_permutationMsg($ymlData, $bind);
	}

	/**
	 * 設定ファイル読み込み
	 */
	public static function getEnvironment()
	{
		$args = func_get_args();

		// 環境変数取得
		self::$server_name = getenv('HOST_NAME') ? getenv('HOST_NAME') : 'localhost';
		array_unshift($args, self::$server_name);

		self::_doLoadingYML('environment');
		$ymlData = self::_searchYmlData($args);

		if (is_null($ymlData)) {
			$args[0] = 'DEFAULT';
			$ymlData = self::_searchYmlData($args);
		}

		return $ymlData;
	}


	public static function searchYmlKeyData($ymlData, $label_column)
	{
		$output = array();
		foreach ($ymlData as $column => $label) {
			if (!array_key_exists($label_column, $label)) {
				break;
			}
			if ($column != '__info') {
				$output[$column] = $label[$label_column];
			}
		}
		return $output;
	}

	/**
	 * YAMLから取得した文言を置換する
	 *
	 * @param unknown_type $err_msg 置換前エラーメッセージ
	 * @param unknown_type $param 置換対象文字列
	 * Ex. permutationErrMsg('%1$sは文言です。', array('置換文字列1'))
	 */
	private static function _permutationMsg($err_msg, Array $param)
	{
		if (!$param) {
			return $err_msg;
		}

		// 入力された置換
		$err_msg = vsprintf($err_msg, $param);
		return $err_msg;
	}

	private static function _searchYmlData($args)
	{
		$ymlData = self::$yml_data;
		foreach ($args as $keyName) {
			if (!is_array($ymlData) || !isset($ymlData[$keyName])) {
				return null;
			}
			$ymlData = $ymlData[$keyName];
		}
		return $ymlData;
	}
}
