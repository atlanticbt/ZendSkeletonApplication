<?php

namespace Application\Service;

/**
 * service: create_account_service
 */
class CreateAccountListener extends BaseService
{

	/**
	 * Attach listeners
	 */
	public function latch()
	{
		/* @var $eventManager \Zend\EventManager\SharedEventManager */
		$eventManager = $this->getServiceLocator()->get('SharedEventManager');
		$eventManager->attach('*', 'register', array($this, 'onRegister'));
//		$zfcServiceEvents = $this->getServiceLocator()->get('zfcuser_user_service')->getEventManager();
//		$zfcServiceEvents->attach('register', array($this, 'onRegister'));
	}

	public function onRegister($e)
	{
		/* @var $user \Application\Entity\Base\User */
		$user = $e->getParam('user');  // User account object
		$form = $e->getParam('form');  // Form object
		/* @var $invite \Application\Service\Invitation */
		$invite = $this->getServiceLocator()->get('invitation');
		if (!$invite->send($user, false)) {
			$e->stopPropagation();
			return false;
		}
	}

}

