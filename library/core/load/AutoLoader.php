<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Mk\Core;

class AutoLoader
{
	protected static $_coreNamespace = 'Mk\\Core';
	protected static $_includeMap = array();

	public function __construct()
	{
		$this->_setting();
	}

	private function _setting()
	{
		spl_autoload_register(array($this, 'myLoader'));
	}

	private static function _isCoreClass($className)
	{
		return strpos($className, static::$_coreNamespace . '\\', 0) === 0;
	}

	private static function _isNamespace($className)
	{
		return strpos($className, '\\') !== 0;
	}

	protected static function _loadCoreClass($className)
	{
		if (!array_key_exists($className, static::$_includeMap)) {
			$dirIterator = new \RecursiveDirectoryIterator(DIR_CORE_LIBRARY);
			$iterator = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST, \FilesystemIterator::SKIP_DOTS);

			foreach ($iterator as $node) {
				if (!$node->isFile()) {
					continue;
				}
				$pathinfo = pathinfo($node->getFilename());
				if (array_key_exists('extension', $pathinfo)
					&& $pathinfo['extension'] == 'php'
					&& ($className === static::$_coreNamespace . '\\' . $pathinfo['filename'])
				) {
					static::$_includeMap[$className] = $node->getPath() . '/' . $node->getFilename();
				}
			}
		}
		return;
	}

	protected static function _loadClass()
	{
		// TODO:同一ファイル名の考慮
		foreach (array('application', 'config', 'library') as $childDir) {
			$dirIterator = new \RecursiveDirectoryIterator(ROOT_PATH . '/' . $childDir);
			$iterator = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST, \FilesystemIterator::SKIP_DOTS);

			foreach ($iterator as $node) {
				if (!$node->isFile() || (strpos($node->getPath(), DIR_CORE_LIBRARY, 0) === 0)) {
					continue;
				}

				$pathinfo = pathinfo($node->getFilename());
				if (array_key_exists('extension', $pathinfo)
					&& $pathinfo['extension'] == 'php'
				) {
					static::$_includeMap[$pathinfo['filename']] = $node->getPath() . '/' . $node->getFilename();
				}
			}
		}
	}

	private function myLoader($className)
	{
		if (static::_isCoreClass($className)) {
			static::_loadCoreClass($className);
		} else {
			static::_loadClass($className);
		}

		if (array_key_exists($className, static::$_includeMap)) {
			require_once(static::$_includeMap[$className]);
			return;
		}

		// 名前空間対応
		if (static::_isNamespace($className)) {
			$className = substr($className, strrpos($className, '\\') + 1);
			if (array_key_exists($className, static::$_includeMap)) {
				require_once(static::$_includeMap[$className]);
				return;
			}
		}
		throw new \Exception('指定されたファイルが存在しません。'. $className);
	}
}