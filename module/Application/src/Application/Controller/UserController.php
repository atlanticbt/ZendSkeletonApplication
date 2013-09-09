<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class UserController extends AbstractActionController
{

	const ROUTE_USER_MANAGE = 'user-manage-route';

	/**
	 * Display list of site users to manage
	 * @return type
	 */
	public function indexAction()
	{
		/* @var $factory \Application\Factory\Search\User */
		$factory = $this->getServiceLocator()->get('user_search_factory');
		$data = $factory->getService()->results();
		return $this->getRequest()->isPost() ? new JsonModel($data) : new ViewModel($data);
	}

	public function createAction()
	{
		return $this->_manageUserAction(true);
	}

	public function manageAction()
	{
		return $this->_manageUserAction();
	}

	protected function _manageUserAction($creating = false)
	{
		/* @var $factory \Application\Factory\Update\User */
		$factory = $this->getServiceLocator()->get('user_update_factory');
		/* @var $service \Application\Service\Update\User */
		$service = $factory->creating($creating)->getService();
		if ($this->getRequest()->isPost()) {
			$service->update();
			return $this->jsonResponse($service->success(), $service->message(), $service->responseData());
		}
		return new ViewModel(array('form' => $service->form(), 'entity' => $service->entity()));
	}

}
