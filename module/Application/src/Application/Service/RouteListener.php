<?php

namespace Application\Service;

use Zend\Permissions\Acl\Acl;
use Zend\Mvc\MvcEvent;
use ZfcUser\Controller\UserController;
use Application\Entity\Base\UserInterface;

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
			if (static::DEBUG) {
				exit("({$requestedResource}) is available to you!");
			}
			// make sure the user is configured
			return $this->_checkUserConfigured($e, $user, $requestedResource, $requestedPrivilege);
		} else if ($user->isNull()) {
			if (static::DEBUG) {
				exit("({$requestedResource}) is not available for guests.");
			}
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

	protected function _checkUserConfigured(MvcEvent $e, UserInterface $user, $routeName, $action)
	{
		if ($user->isNull()) {
			return;
		}
		if ($user->getPassword() == null) {
			if ($routeName != UserController::ROUTE_CHANGEPASSWD) {
				return $this->_sendToRouteNamed($e, UserController::ROUTE_CHANGEPASSWD);
			}
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

