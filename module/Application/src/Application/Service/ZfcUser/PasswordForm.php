<?php

namespace Application\Service\ZfcUser;

use Application\Service\BaseService;
use Application\Form\ChangePasswordFilter;

class PasswordForm extends BaseService
{

	public function getForm()
	{
		$sm = $this->getServiceLocator();
		$options = $sm->get('zfcuser_module_options');
		/**
		 * If the user is logged in and has their password set to null,
		 * do not require them to provide the 'old password' field.
		 */
		$requireOldPass = $this->_currentUser()->getPassword() != null;
		$form = new \ZfcUser\Form\ChangePassword(null, $sm->get('zfcuser_module_options'));
		if (!$requireOldPass) {
			// removing the old password field
			$form->remove('credential');
		}
		$form->setInputFilter(new ChangePasswordFilter($options, $requireOldPass));
		return $form;
	}

}

