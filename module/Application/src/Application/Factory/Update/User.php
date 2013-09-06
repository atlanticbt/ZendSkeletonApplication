<?php

namespace Application\Factory\Update;

/**
 * service: user_update_factory
 */
class User extends UpdateFactory
{

	protected function _getUpdateService()
	{
		$service = $this->_isCreatingUser() ? 'user_create_service' : 'user_update_service';
		return $this->getServiceLocator()->get($service);
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
		if ($this->_isCreatingUser()) {
			return $this->_getNewEntity();
		}
		$entity = parent::_getEntity();
		if (empty($entity)) {
			return $this->_currentUser();
		}
		return $entity;
	}

	protected function _isCreatingUser()
	{
		return $this->_getParam('action') == 'create';
	}

}

