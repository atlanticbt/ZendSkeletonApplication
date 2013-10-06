<?php

namespace Application\Service\Update\User;

use Application\Service\Update\User as UserUpdateService;
use Application\Entity\Base\UserInterface;
use Application\Service\Update\Exception\UnsetEntity as UnsetEntityException;

/**
 * service: user_forgot_service
 */
class Forgot extends UserUpdateService
{

	/**
	 * In the event a user submits their email to reset their account password,
	 * look up a user with this email and set to entity. Otherwise, use the
	 * entity that was provided by the factory.
	 * @return type
	 */
	protected function _getEntity()
	{
		if (empty($this->_entity) || $this->_entity->isNull()) {
			$email = $this->_getParam('email');
			if (empty($email)) {
				return parent::_getEntity();
			}
			$this->_entity = $this->_entityManager()->getRepository('Application\Entity\Base\User')->findOneBy(array(
				'email' => $email
			));
		}
		return parent::_getEntity();
	}

	/**
	 * If _getEntity returned a null user, throw unset entity exception
	 * @return \Application\Service\Update\User\Forgot
	 * @throws UnsetEntityException
	 */
	protected function _prepare()
	{
		if ($this->_getEntity()->isNull()) {
			throw new UnsetEntityException('Unable to find user to reset');
		}
		return $this;
	}

	/**
	 * Send reset invitation.
	 * @return \Application\Service\Update\User\Forgot
	 */
	protected function _validate()
	{

		/* @var $invitation \Application\Service\Invitation */
		$invitation = $this->getServiceLocator()->get('invitation');
		if ($invitation->setEmailTemplate('email/forgot.phtml')->send($this->_getEntity(), true)) {
			$this->_success = true;
			$this->_entityManager()->flush();
		} else {
			$this->_success = false;
			$this->_message = 'Unable to send reset email at this time.';
		}
		return $this;
	}

}

