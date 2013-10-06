<?php

namespace Application\Service\Update;

use Application\Controller\UserController as ManageUserController;
use Application\Entity\BaseInterface;
use Application\Service\Permission;
use Application\Service\Update;
use Zend\Form\Form;

/**
 * service: user_update_service
 */
class User extends Update
{

	public function __construct() {
		$this->_formConfigCallables[static::BASE_FORM_NAME] = array($this, '_configureUserForm');
	}

	public function _configureUserForm($key, Form $form, BaseInterface $entity){
		/* @var $permission \Application\Service\Permission */
		$permission = $this->getServiceLocator()->get('permission_service');
		if (!$permission->allowed(ManageUserController::ROUTE_USER_MANAGE)) {
			$form->remove('state');
		}
		if (!$permission->allowed(Permission::RESOURCE_MANAGE_USER_TYPE, $entity->getRole())) {
			foreach ($form->getElements() as $element) {
				$element->setAttribute('disabled','disabled');
			}
		}
		return $form;
	}

	protected function _postFormValidate() {
		/* @var $permission \Application\Service\Permission */
		$permission = $this->getServiceLocator()->get('permission_service');
		if (!$permission->allowed(Permission::RESOURCE_MANAGE_USER_TYPE, $this->_getEntity()->getRole())) {
			$this->_success = false;
			$this->_message = array('user' => array('notPermitted' => 'You do not have permission to modify this user'));
		}
	}

	public function isBatch($batch = null)
	{
		return $this;
	}

}

