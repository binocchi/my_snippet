<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Cli;

abstract class BaseEntity
{
	protected $_returnArray = array();
	protected $_curlResource;

	public function __construct()
	{
		// リクエスト先の設定
		$this->_curlResource = curl_init(\YmlLoader::getEnvironment('url'));

		// 出力結果の設定
		curl_setopt($this->_curlResource, CURLOPT_RETURNTRANSFER, true);

		// 共通の設定値を格納
		// 例) $this->_version = YmlLoader::getEnvironment('application_info', 'version');

	}

	/**
	 * ポスト用の配列に変換
	 */
	public function toArray()
	{
		return array_merge(
			$this->_returnArray,
			array(
				'version' => $this->_version,
			)
		);
	}

	/**
	 * 通信
	 */
	public function get()
	{
		// POSTパラメータの設定
		curl_setopt($this->_curlResource, CURLOPT_POSTFIELDS, $this->toArray());

		// レスポンス値の返却
		return curl_exec($this->_curlResource);
	}

}