<?php

namespace Application\Service\Update\User;

use Application\Service\Update\User as UserUpdateService;
use Application\Entity\Base\UserInterface;

/**
 * service: user_reset_service
 */
class Reset extends UserUpdateService
{

	protected function _getEntity()
	{
		if (empty($this->_entity) || $this->_entity->isNull()) {
			$userHash = $this->_getParam('userHash'); // the hashed user string
			$loginHash = $this->_getParam('loginHash'); // the hashed user token
			if (empty($userHash) || empty($loginHash)) {
				return parent::_getEntity();
			}
			$tokenMatches = $this->_entityManager()->getRepository('Application\Entity\Base\User')->findBy(array(
				'loginHash' => $loginHash
			));
			// hopefully there will only ever be one, but collisions do occur.
			/* @var $token \Application\Service\Token */
			$token = $this->getServiceLocator()->get('token');
			/* @var $tokenMatch \Application\Entity\Base\User */
			foreach ($tokenMatches as $tokenMatch) {
				if ($token->generate($tokenMatch)->getUserHash() == $userHash) {
					$this->_entity = $tokenMatch;
				}
			}
		}
		return parent::_getEntity();
	}

	protected function _prepare()
	{
		$user = $this->_getEntity();

		/* @var $user \Application\Entity\Base\User */
		// one time login, clear the hash
		$user->setLoginHash(null);
		// activate the user account, if they were inactive
		if ($user->getState() == UserInterface::STATE_INACTIVE) {
			$user->setState(UserInterface::STATE_ACTIVE);
			$this->_message = 'Your user account has been activated';
		}
		// if resetting password, clear the existing password
		if ($this->_getParam('reset')) {
			$user->setPassword(null);
			$this->_message = 'Your password has been reset';
		}
		return $this;
	}

	protected function _validate()
	{
		$this->_success = true;
		$this->_entityManager()->flush();
		return $this;
	}

	protected function _postValidate()
	{
		$this->getServiceLocator()->get('zfcuser_auth_service')->getStorage()->write($this->_getEntity()->getEId());
		return parent::_postValidate();
	}

}

