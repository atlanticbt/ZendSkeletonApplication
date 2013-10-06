<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

class IndexController extends AbstractActionController
{

	public function indexAction()
	{
		return new ViewModel();
	}

	public function dashboardAction()
	{
		return new ViewModel();
	}

	public function cronAction() {
		$request = $this->getRequest();
		// Make sure that we are running in a console and the user has not tricked our
		// application into running this action from a public web server.
		if (!$request instanceof ConsoleRequest){
			throw new \RuntimeException('You can only use this action from a console!');
		}
        echo 'Cron process completed';
	}

}
