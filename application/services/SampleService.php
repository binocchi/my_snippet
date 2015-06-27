<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Cli;

class SampleService extends \Mk\Core\BaseService
{
	public function index()
	{
		$scenarioEntity = new \Cli\CommonEntity();
		$this->setResponseData('weather', $scenarioEntity->get());
	}
}