<?php

namespace Application\Service;

use Application\Service\BaseService;
use Application\Entity\Base\UserInterface;
use Application\Controller\UserController;

/**
 * service: token
 */
class Token extends BaseService
{

	const USER_SALT = 'F3alGu8!';

	private $_userHash = null;
	private $_token = null;

	/**
	 * Generates two hashes for the specified user.
	 * @param \DocurepUser\Entity\User $user
	 * @return \DocurepUser\Service\Token
	 */
	public function generate(UserInterface $user)
	{
		$this->_userHash = md5(static::USER_SALT . $user->getEmail());
		$this->_token = sha1($user->getEmail() . time());
		return $this;
	}

	public function getUserHash()
	{
		return $this->_userHash;
	}

	public function getToken()
	{
		return $this->_token;
	}

	public function url($reset = false)
	{

		return $this->getServiceLocator()->get('router')->assemble(array(
					'userHash' => $this->_userHash,
					'loginHash' => $this->_token,
					'reset' => $reset,
						), array('name' => UserController::ROUTE_RESET, 'force_canonical' => true));
	}

}

