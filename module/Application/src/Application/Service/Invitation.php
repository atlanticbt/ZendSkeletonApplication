<?php

namespace Application\Service;

//use Zend\Stdlib\RequestInterface;
//use Zend\View\Model\JsonModel;
//use Application\Factory\Role\Exception\InvalidUserRole as InvalidRoleException;

use Application\Entity\Base\UserInterface;
use Application\Service\Invitation\Exception\InvalidConfiguration as InvalidEmailConfigException;

/**
 * service: invitation
 */
class Invitation extends BaseService
{

	/**
	 * @var array
	 */
	protected $_inviteEmailConfig;

	/**
	 * @var string
	 */
	protected $_emailTemplate;
	/**
	 * @var array
	 */
	protected $_emailTemplateParams;

	/**
	 * Send the user an invitation email.
	 * @NOTE: this call has to come after the user email has been set
	 * in order to generate the user has correctly.
	 * @param type $user
	 * @return type
	 */
	public function send(UserInterface $user, $reset = true)
	{
		if ($user->isNull()) {
			return $this;
		}
		$config = $this->_getInviteEmailConfig();

		/* @var $pair \Application\Service\Token */
		$pair = $this->getServiceLocator()->get('token');
		$pair->generate($user);
		$user->setLoginHash($pair->getToken());

		$htmlMarkup = $this->getServiceLocator()
				->get('viewrenderer')
				->render($this->_getEmailPartial(), array_merge($this->_getEmailPartialParams(), array('resetUrl' => $pair->url($reset), 'user' => $user)));


		/* @var $email \Application\Service\Email */
		$email = $this->getServiceLocator()->get('email');
		$email->reset();
		return $email->addTo($user->getEmail())
						->setSubject($config['subject'])
						->setBody($htmlMarkup)
						->send();
	}
	
	
	protected function _getEmailPartial() {
		if (empty($this->_emailTemplate)) {
			$config = $this->_getInviteEmailConfig();
			$this->_emailTemplate = $config['invite_email_partial'];
		}
		return $this->_emailTemplate;
	}

	protected function _getEmailPartialParams() {
		if (empty($this->_emailTemplateParams)) {
			$config = $this->_getInviteEmailConfig();
			$this->_emailTemplateParams = array('companyName' => $config['company_name']);
		}
		return $this->_emailTemplateParams;
	}

	public function setEmailTemplate($partialName, $params = array()) {
		$this->_emailTemplate = $partialName;
		$this->_emailTemplateParams = $params;
		return $this;
	}

	protected function _getInviteEmailConfig() {
		if (empty($this->_inviteEmailConfig)) {
			// get the config
			$config = $this->getServiceLocator()->get('config');
			if (!isset($config['invite_email'])) {
				throw new InvalidEmailConfigException('The invite email configuration is not in place.');
			}
			$this->_inviteEmailConfig = $config['invite_email'];
		}
		return $this->_inviteEmailConfig;
	}

}

