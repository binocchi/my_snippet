<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Mk\Core;

abstract class BaseService
{
	const RESPONSE_KEY_DATA = 'data';
	protected $_response = null;

	public function setResponseData($key, $value)
	{
		$this->_response[self::RESPONSE_KEY_DATA][$key] = $value;
	}

	public function getResponse()
	{
		return $this->_response;
	}

	public function pre()
	{
		$this->_response = array(
			static::RESPONSE_KEY_DATA => array(),
		);
	}
}