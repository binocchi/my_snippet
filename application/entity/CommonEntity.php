<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Cli;

class CommonEntity extends \Cli\BaseEntity
{
	protected $_params;

	/**
	 * パラメータの設定
	 */
	public function setParams($params)
	{
		$this->_params = $params;
	}

	/**
	 * ポスト用の配列に変換
	 */
	public function toArray()
	{
		if (is_array($this->_params)) {
			$this->_returnArray = $this->_params;
		}
		return parent::toArray();
	}
}