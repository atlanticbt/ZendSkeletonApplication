<?php

namespace Application\Service\Update\User;

use Application\Entity\Base\User as UserEntity;
use Application\Service\Update\User as UserUpdateService;

/**
 * service: user_create_service
 */
class Create extends UserUpdateService
{

	public function __construct()
	{
		$this->_validationGroup = array(
			static::BASE_FORM_NAME => array(
				'email', 'username', 'state',
			),
		);
	}

	protected function _prePrepare()
	{
		$this->_setFormData('state', UserEntity::STATE_ACTIVE);
	}

}

