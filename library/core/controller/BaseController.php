<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Mk\Core;

abstract class BaseController
{
	protected $_service;
	protected $_isOutputProcess = true;
	protected $_outputString;

	public function __construct()
	{
		$serviceName = substr_replace(get_class($this), 'Service', -mb_strlen('Controller'));
		$this->_service = new $serviceName();
	}

	/**
	 * 事前処理
	 */
	public function preAction()
	{
		$this->_service->pre();
	}

	/**
	 * 事後処理
	 */
	public function postAction()
	{
		$response = $this->_service->getResponse();
		foreach ($response[BaseService::RESPONSE_KEY_DATA] as $data)
		{
			$this->_outputString .= $data;
		}
	}

	/**
	 * 結果出力
	 */
	public function output($tmpFile = null)
	{
		if(!$this->_isOutputProcess)
		{
			return;
		}
		echo $this->_outputString;
	}
}