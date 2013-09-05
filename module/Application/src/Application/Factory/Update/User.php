<?php

namespace Application\Factory\Update;

/**
 * service: user_update_factory
 */
class User extends UpdateFactory
{

	protected function _getUpdateService()
	{
		return $this->getServiceLocator()->get('user_update_service');
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
		$entity = parent::_getEntity();
		if (empty($entity)) {
			return $this->_currentUser();
		}
		return $entity;
	}

}

