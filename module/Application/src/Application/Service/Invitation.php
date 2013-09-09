<?php

namespace Application\Service;

use Zend\Stdlib\RequestInterface;
use Zend\View\Model\JsonModel;
use Application\Factory\Role\Exception\InvalidUserRole as InvalidRoleException;

abstract class Invitation extends BaseService
{

	const PERMISSION_INVITE = 'invite-user-permission';
	const EVENT_USER_INVITE = 'invite-user';

	protected $_success;
	protected $_message;

	/**
	 * The data array which is used to construct the new user
	 * @var array
	 */
	private $_data;

	/**
	 * The data array which will can populate a json response regarding this invitation.
	 * @var array
	 */
	protected $_jsonData;
	/* @var \DocurepUser\Entity\User */
	private $_invited;
	private $_request;

	public function json()
	{
		return new JsonModel(array_merge($this->getJsonData(), array(
					'success' => $this->success(),
					'msg' => $this->message(),
		)));
	}

	/**
	 * Creates and then sends invitation based on request data
	 */
	public abstract function process();

	public function setRequest(RequestInterface $request)
	{
		$this->_request = $request;
		return $this;
	}

	/**
	 *
	 * @return RequestInterface
	 */
	public function getRequest()
	{
		return $this->_request;
	}

	public function message()
	{
		return $this->_message;
	}

	public function success()
	{
		return $this->_success;
	}

	public function getInvitedUser()
	{
		return $this->_invited;
	}

	public function getJsonData()
	{
		return empty($this->_jsonData) ? array() : $this->_jsonData;
	}

	public function __invoke()
	{
		return $this;
	}

	public function getRole()
	{
		return $this->getRequest()->getPost('role');
	}

	/**
	 * Is the current user permitted to create users of this role.
	 * @param string $role
	 * @return boolean
	 */
	protected function permitted($role)
	{
		/* @var $acl \Zend\Permissions\Acl\Acl */
		$acl = $this->getServiceLocator()->get('permission_service')->getAcl();
		if ($acl->isAllowed($this->_currentUser()->getRole(), static::PERMISSION_INVITE, $role)) {
			return true;
		}
		$this->_message = 'You do not have permission to perform this action';
		return false;
	}

	/**
	 * Creates a user from the data
	 * @param type $data
	 */
	protected function create($data)
	{
		$this->_reset();
		$this->_data = $data;
		$this->_message = 'You do not have permission to perform this action';
		try {
			// create the user entity
			$userFactory = new \Application\Factory\Role();
			$user = $userFactory->createUser($this->getRole());
			// ensure it meets minimum requirements: first/last name and email
			/* @var $entityToForm \Application\Service\EntityToForm */
			$entityToForm = $this->getServiceLocator()->get('e2f');
			$userForm = $entityToForm->convertEntity($user);

			$userForm->setValidationGroup(array('firstName', 'lastName', 'email'));

			$userForm->setData($data);
			if (!$userForm->isValid()) {
				$this->_message = $userForm->getMessages();
			} else {
				$em = $this->entityManager();
				// check for duplicate emails
				$conflict = $em->getRepository('DocurepUser\Entity\User')->findOneBy(array('email' => $user->getEmail()));
				if (empty($conflict)) {
					/* @var $hook \Application\Service\EventHook */
					$hook = $this->getServiceLocator()->get('event_hook');
					// allow other modules to do additional verification.
					if ($hook(static::EVENT_USER_INVITE . $user->getRole(), $this, array('user' => $user, 'data' => $data, 'serviceLocator' => $this->getServiceLocator()))->wasStopped()) {
						$this->_message = $hook->getInterrupt();
					} else {
						// no interrupts
						$em->persist($user);
						$this->_message = 'User created.';
						$this->_success = true;
						$this->_invited = $user;
					}
				} else {
					$this->_message = 'A user with the email address "' . $data['email'] . '" already exists.';
				}
			}
		} catch (InvalidRoleException $e) {
			$this->_message = 'The specified user role is invalid';
		}
		return $this;
	}

	/**
	 * Send the user an invitation email.
	 * @NOTE: this call has to come after the user email has been set
	 * in order to generate the user has correctly.
	 * @param type $user
	 * @return type
	 */
	protected function send(\DocurepUser\Entity\User $user = null)
	{
		$this->_success = false;
		if (empty($user)) {
			return $this;
		}
		$this->_message = 'Invitation could not be sent.';
		/* @var $pair \DocurepUser\Service\Token */
		$pair = $this->getServiceLocator()->get('user_token');
		$pair->generate($user);
		$user->setAccountToken($pair->getToken())
				->setAccountTokenTs('now');

		$resetUrl = $pair->url(true);

		$htmlMarkup = '
<h1>Your Docurep Invitation!</h1>
<p>
	You are receiving this email because a docurep representative has entered you into our system. This is an invite to join something great!
	To set up your <strong>Docurep Account</strong>, <a href="' . $resetUrl . '">click here</a> or paste the following URL into your browser.<br /><br />
		' . $resetUrl . '
</p>';
		/* @var $email \DocurepUser\Service\Email */
		$email = $this->getServiceLocator()->get('user_email');
		$this->_success = $email->addTo($user->getEmail())
				->setSubject('Docurep Account Invitation')
				->setBody($htmlMarkup)
				->send();
		$responseData = null;
		if ($this->success()) {
			$this->entityManager()->flush();
			$this->_message = 'Invitation sent to ' . $user->getEmail();
			/* @var $hook \Application\Service\EventHook */
			$hook = $this->getServiceLocator()->get('event_hook');
			$responseData = array('user' => $user->flatten());
			if ($hook(static::EVENT_USER_INVITE . $user->getRole() . '.post', $this, array('user' => $user, 'data' => $this->_data, 'serviceLocator' => $this->getServiceLocator()))->wasStopped()) {
				$responseData = array_merge($responseData, $hook->getInterrupt());
			}
		}
		$this->_jsonData = $responseData;
		return $this;
	}

	protected function _reset()
	{
		$this->_success = false;
		$this->_invited = null;
		$this->_jsonData = null;
		$this->_message = null;
	}

	public function setEventManager(\Zend\EventManager\EventManagerInterface $events)
	{
		parent::setEventManager($events);
		$this->_addEventIdentifier(__CLASS__);
		return $this;
	}

}

