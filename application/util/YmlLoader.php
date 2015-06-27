<?php
/**
 * Created by PhpStorm.
 * User: M.K
 * -----------------------------------------
 * 親クラス機能一覧
 * 使用方法や詳細については親クラス baseYmlLoaderを参照すること
 *
 * [protected 関数]
 *  - parent::_doLoadingYML($yml_name);
 *    confディレクトリ配下のYMLファイルをロードする
 *    ex ) /conf/test/hogehoge.ymlを読み込む場合
 *         parent::_doLoadingYML('test/hogehoge');
 *
 * [public 関数]
 *  - ymlLoader::getValueAll($arg1, $arg2, ...);
 *  - ymlLoader::getAllByArray($ymlName, array $args);
 *    指定ファイル, 指定階層のデータ取得
 *
 *  - ymlLoader::getValueText($arg1, $arg2, ...);
 *    指定ファイル, 指定階層のデータ取得, 文字列置換可
 *
 *  - ymlLoader::getEnvironment();
 *    host毎の環境設定取得
 *
 *  - ymlLoader::getActionYml($action, $data);
 *    画面毎の設定取得
 */
class YmlLoader extends \Mk\Core\BaseYmlLoader
{
	/**
	 * getValueTextのラッパークラス
	 * バインド値をURLエンコードする
	 */
	public static function getValueUrl()
	{
		$args = func_get_args();
		if (is_array(end($args))) {
			$bind = array_pop($args);

			foreach ($bind as $key => $value) {
				$bind[$key] = rawurlencode($value);
			}
			array_push($args, $bind);
		}
		return call_user_func_array('self::getValueText', $args);
	}
}
