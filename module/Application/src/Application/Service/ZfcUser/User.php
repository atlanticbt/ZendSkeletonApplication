<?php

namespace Application\Service\ZfcUser;

use Zend\Crypt\Password\Bcrypt;

/**
 * service: zfcuser_user_service
 */
class User extends \ZfcUser\Service\User
{

	/**
	 * Overriding the default implementation to allow for reset passwords in the event of a forgotten password
	 * @param array $data
	 * @return boolean
	 */
	public function changePassword(array $data)
	{
		/* @var $currentUser \Application\Entity\Base\User */
		$currentUser = $this->getAuthService()->getIdentity();

		$oldPass = isset($data['credential']) ? $data['credential'] : null;
		$newPass = $data['newCredential'];

		$bcrypt = new Bcrypt;
		$bcrypt->setCost($this->getOptions()->getPasswordCost());
		if ($currentUser->getPassword() !== null && !$bcrypt->verify($oldPass, $currentUser->getPassword())) {
			return false;
		}

		$pass = $bcrypt->create($newPass);
		$currentUser->setPassword($pass);

		$this->getEventManager()->trigger(__FUNCTION__, $this, array('user' => $currentUser));
		$this->getUserMapper()->update($currentUser);
		$this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array('user' => $currentUser));

		return true;
	}

}

