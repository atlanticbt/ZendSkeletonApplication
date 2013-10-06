<?php

namespace Application\Factory;

use Application\Factory\Role\Exception\InvalidUserRole as InvalidUserRoleException;
use Application\Service\Permission;

/**
 *
 */
class Role
{

	public function getClassFromRole($role)
	{
		switch ($role) {
			case Permission::ROLE_USER:
				return '\Application\Entity\Base\User';
				break;
			case Permission::ROLE_ADMIN:
				return '\Application\Entity\Base\User\Admin';
				break;
			case Permission::ROLE_SUPER:
				return '\Application\Entity\Base\User\Super';
				break;
		}
		throw new InvalidUserRoleException('Unable to create user of role ' . $role);
	}

	public function createUser($role)
	{
		$class = $this->getClassFromRole($role);
		return new $class();
	}

}

