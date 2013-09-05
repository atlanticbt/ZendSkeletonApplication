<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class UserController extends AbstractActionController
{

	const ROUTE_USER_MANAGE = 'user-manage-route';

	public function indexAction()
	{
		return new ViewModel();
	}

	public function manageAction()
	{
		/* @var $factory \Application\Factory\Search\User */
		$factory = $this->getServiceLocator()->get('user_search_factory');
		$data = $factory->getService()->results();
		return $this->getRequest()->isPost() ? new JsonModel($data) : new ViewModel($data);
	}

}
