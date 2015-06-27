<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
$ROOT_PATH = dirname(__DIR__);

define('ROOT_PATH', $ROOT_PATH);
define('DIR_APPLICATION',  ROOT_PATH . '/application/');
define('DIR_LIBRARY',      ROOT_PATH . '/library/');
define('DIR_CONFIG',       ROOT_PATH . '/config/');
define('DIR_CONFIG_YML',   ROOT_PATH . '/config/yml/');
define('DIR_LOG',          ROOT_PATH . '/log/');
define('DIR_TMP',          ROOT_PATH . '/tmp/');
define('DIR_CORE_LIBRARY', DIR_LIBRARY . 'core/');
define('DIR_YML_CACHE',    DIR_TMP . 'yml_cache/');

define('DEFAULT_DB', 'mysql');
define('DB_HOST',    'localhost');
define('DB_NAME',    'development');
define('DB_USER',    'root');
define('DB_PASS',    '');

define('FILE_FORMAT_DATE',     'Ymd');
define('FILE_FORMAT_DATETIME', 'YmdHis');