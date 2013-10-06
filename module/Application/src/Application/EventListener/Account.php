<?php

namespace Application\EventListener;

use Application\Service\BaseService;
use Zend\EventManager\Event;
use Zend\Authentication\Result as AuthResult;
use Zend\Stdlib\RequestInterface as Request;
use Doctrine\ORM\NoResultException;
use Application\Entity\Base\UserInterface;
use Application\Entity\Base\UserNull;

/**
 * service: listener_account
 */
class Account extends BaseService
{

	public function latch()
	{
		// attach to account events.
		$eventManager = $this->getServiceLocator()->get('SharedEventManager');
		// listen for any authentication events.
		$eventManager->attach('*', 'authenticate', array($this, '_onLoginAttempt'));
	}

	public function _onLoginAttempt(Event $e)
	{
		$code = $e->getParam('code');
		switch ($code) {
			case AuthResult::FAILURE_CREDENTIAL_INVALID:
				$this->_loginFailed($e);
				break;
			case AuthResult::FAILURE:
			case AuthResult::FAILURE_IDENTITY_AMBIGUOUS:
			case AuthResult::FAILURE_IDENTITY_NOT_FOUND:
			case AuthResult::FAILURE_UNCATEGORIZED:
				// do nothing
				break;
			case AuthResult::SUCCESS:
				$this->_loginSucceeded($e);
				break;
		}
	}

	/**
	 * Manage the user state/failed login stats.
	 * @param \Zend\EventManager\Event $e
	 *
	 */
	protected function _loginFailed(Event $e)
	{
        // do nothing
	}

	/**
	 * Reset the user failed login attempt status.
	 * @param \Zend\EventManager\Event $e
	 */
	protected function _loginSucceeded(Event $e)
	{
        // do nothing
	}

	/**
	 *
	 * @param \Doctrine\ORM\NoResultException $e
	 * @return \Application\Entity\Base\UserInterface
	 */
	protected function _getLoginEventUser(Event $e)
	{
		try {
			return $this->_entityManager()
							->createQuery('SELECT u FROM Application\Entity\Base\User u WHERE u.email LIKE :id OR u.username LIKE :id')
							->setParameter('id', $e->getParam('request')->getPost('identity'))
							->getSingleResult();
		} catch (NoResultException $e) {
			return new UserNull();
		}
	}

	/**
	 *
	 * @return \Zend\Mvc\Controller\Plugin\FlashMessenger
	 */
	protected function _getFlashMessenger()
	{
		return $this->getServiceLocator()
						->get('ControllerPluginManager')
						->get('flashmessenger');
	}

}
