<?php

namespace Application\Service;

use Zend\Permissions\Acl\Acl;
use Zend\Mvc\MvcEvent;
use ZfcUser\Controller\UserController;

class RouteListener extends BaseService
{

	const DEBUG = false;

	public function onRoute(MvcEvent $e)
	{
		// check the ACL if route is permitted to this user.
		$requestedResource = $e->getRouteMatch()->getMatchedRouteName();
		$requestedPrivilege = $e->getRouteMatch()->getParam('action');
		/* @var $permissionService \Application\Service\Permission */
		$permissionService = $this->getServiceLocator()->get('permission_service');
		/* @var $acl Acl */
		$acl = $permissionService->getAcl();
		$e->getViewModel()->setVariable('acl', $acl);
		if (!$acl->hasResource($requestedResource)) {
			if (static::DEBUG) {
				exit("({$requestedResource}) does not exist");
			}
			return $this->_sendToRouteNamed($e, 'home');
		}
		$user = $this->_currentUser();
		if ($acl->isAllowed($user->getRole(), $requestedResource, $requestedPrivilege)) {
			// make sure the user is configured
			return;
		} else if ($user->isNull()) {
			// user is not logged in
			return $this->_sendToRouteNamed($e, UserController::ROUTE_LOGIN);
		} else {
			if (static::DEBUG) {
				exit("({$requestedResource}) is not available for your user role.");
			}
			// user is logged in and does not have permission.
			return $this->_sendToRouteNamed($e, 'home');
		}
	}

	protected function _sendToRouteNamed(MvcEvent $e, $routeName)
	{
		$url = $e->getRouter()->assemble(array(), array('name' => $routeName));
		$response = $e->getResponse();
		$response->getHeaders()->addHeaderLine('Location', $url);
		$response->setStatusCode(\Zend\Http\Response::STATUS_CODE_302);
		$response->sendHeaders();
		return $e->stopPropagation();
	}

}

