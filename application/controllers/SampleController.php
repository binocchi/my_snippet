<?php
/**
 * Created by PhpStorm.
 * User: M.K
 */
namespace Cli;

class SampleController extends \Mk\Core\BaseController
{
	public function indexAction()
	{
		$this->_service->index();
	}
}