<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */

// コマンドライン引数の設定
$shortOptions = 'c:a:';
$longOptions  = array(
	'value::',
);
$options = getopt($shortOptions, $longOptions);

if(!array_key_exists('c', $options))
{
	echo "コントローラが設定されていません\n";
	exit;
}
$controllerName = 'Cli\\' . $options['c'] . 'Controller';

// アクション名が指定されていない場合はindexアクションを実行
$actionName = array_key_exists('a', $options) ? $options['a'] . 'Action' : 'indexAction';

require_once __DIR__ . '/config/Define.php';
require_once DIR_LIBRARY . 'core/load/AutoLoader.php';

$loader = new Mk\Core\AutoLoader();

set_error_handler(function ($severity, $message, $filepath, $line) {
	Mk\Core\Logger::setDefaultLogName('error', 'default', __FILE__);
	Mk\Core\Logger::error($severity . ' ' . $message . ' ' . $filepath . ' LINE:' . $line . PHP_EOL);
});

set_exception_handler(function (Exception $e) {
	var_dump($e->getMessage());exit;
	Mk\Core\Logger::setDefaultLogName('error', 'default', __FILE__);
	Mk\Core\Logger::critical( $e->getCode() . ':' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);
});

date_default_timezone_set(\YmlLoader::getEnvironment('timezone'));

$controller = new $controllerName();
$controller->preAction();
$controller->$actionName();
$controller->postAction();
$controller->output();