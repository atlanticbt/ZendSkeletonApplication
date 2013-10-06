<?php

namespace Application\Factory\Update;

/**
 * service: user_update_factory
 */
class User extends UpdateFactory
{

	protected $_creating = false;

	protected function _getUpdateService()
	{
		$serviceKey = 'user_update_service';
		if ($this->creating()) {
			$serviceKey = 'user_create_service';
		} else if ($this->_getParam('action') == 'reset') {
			$serviceKey = 'user_reset_service';
		} else if ($this->_getParam('action') == 'forgot' || $this->_getParam('action') == 'send-reset') {
			$serviceKey = 'user_forgot_service';
		}

		/* @var $service \Application\Service\Update\User */
		$service = $this->getServiceLocator()->get($serviceKey);
		$service->isBatch($this->_getParam('batch') == 1);
		return $service;
	}

	protected function _getEntityClass()
	{
		return 'Application\Entity\Base\User';
	}

	protected function _getEntityIdParam()
	{
		return $this->_getParam('user');
	}

	protected function _getEntity()
	{
		if ($this->creating()) {
			return $this->_getNewEntity();
		}
		$entity = parent::_getEntity();
		if (empty($entity)) {
			return $this->_currentUser();
		}
		return $entity;
	}

	protected function _getNewEntity()
	{
		$roleFactory = new \Application\Factory\Role();
		$user = $roleFactory->createUser($this->_getParam('role'));
		$this->_entityManager()->persist($user);
		return $user;
	}

	public function creating($creating = null)
	{
		if ($creating === null) {
			return $this->_creating;
		}
		$this->_creating = $creating;
		return $this;
	}

}

