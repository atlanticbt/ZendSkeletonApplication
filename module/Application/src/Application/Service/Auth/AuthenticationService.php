<?php

namespace Application\Service\Auth;

use Zend\Authentication\AuthenticationService as ZendAuthService;
use Application\Entity\Base\UserNull;

class AuthenticationService extends ZendAuthService
{

	public function getIdentity()
	{
		$identity = parent::getIdentity();
		if ($identity === null) {
			return new UserNull();
		}
		return $identity;
	}

}

