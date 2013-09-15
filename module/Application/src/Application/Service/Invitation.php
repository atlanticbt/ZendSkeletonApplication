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
		// get the config
		$config = $this->getServiceLocator()->get('config');
		if (!isset($config['invite_email'])) {
			throw new InvalidEmailConfigException('The invite email configuration is not in place.');
		}
		$config = $config['invite_email'];

		/* @var $pair \Application\Service\Token */
		$pair = $this->getServiceLocator()->get('token');
		$pair->generate($user);
		$user->setLoginHash($pair->getToken());

		$htmlMarkup = $this->getServiceLocator()
				->get('viewrenderer')
				->render($config['invite_email_partial'], array('resetUrl' => $pair->url($reset), 'user' => $user, 'companyName' => $config['company_name']));


		/* @var $email \Application\Service\Email */
		$email = $this->getServiceLocator()->get('email');
		$email->reset();
		return $email->addTo($user->getEmail())
						->setSubject($config['subject'])
						->setBody($htmlMarkup)
						->send();
	}

}

