<?php

namespace Application\Factory;

use Application\Factory\Role\Exception\InvalidUserRole as InvalidUserRoleException;
use Application\Service\Permission;
use Application\Entity\Base\User;
use Application\Entity\Base\User\Admin;
use Application\Entity\Base\User\Super;

/**
 *
 */
class Role
{

	public function createUser($role)
	{
		switch ($role) {
			case Permission::ROLE_USER:
				return new User();
				break;
			case Permission::ROLE_ADMIN:
				return new Admin();
				break;
			case Permission::ROLE_SUPER:
				return new Super();
				break;
		}
		throw new InvalidUserRoleException('Unable to create user of role ' . $role);
	}

}

