<?php

use Application\Service\Permission;
use ZfcUser\Controller\UserController as UserController;
use Application\Controller\UserController as ManageUserController;

return array(
	'acl_resource_map' => array(
		Permission::ROLE_GUEST => array(
			'permissions' => array(
				'home' => null,
				UserController::ROUTE_LOGIN => null,
				UserController::ROUTE_REGISTER => null,
				ManageUserController::ROUTE_RESET => null,
				ManageUserController::ROUTE_USER_MANAGE => array('forgot'),
			),
			'bans' => array(),
			'parents' => array(),
		),
		Permission::ROLE_USER => array(
			'permissions' => array(
				UserController::ROUTE_CHANGEEMAIL => null,
				UserController::ROUTE_CHANGEPASSWD => null,
				UserController::CONTROLLER_NAME . '/logout' => null,
			),
			'bans' => array(
				UserController::ROUTE_LOGIN => null,
				UserController::ROUTE_REGISTER => null,
			),
			'parents' => array(Permission::ROLE_GUEST),
		),
		Permission::ROLE_ADMIN => array(
			'permissions' => array(
				ManageUserController::ROUTE_USER_MANAGE => null,
				Permission::RESOURCE_INVITE => array(Permission::ROLE_USER),
                Permission::RESOURCE_MANAGE_USER_TYPE => array(Permission::ROLE_USER),
			),
			'bans' => array(),
			'parents' => array(Permission::ROLE_USER),
		),
		Permission::ROLE_SUPER => array(
			'permissions' => array(
				ManageUserController::ROUTE_USER_MANAGE => null,
				Permission::RESOURCE_INVITE => array(Permission::ROLE_USER, Permission::ROLE_ADMIN, Permission::ROLE_SUPER),
                Permission::RESOURCE_MANAGE_USER_TYPE => array(Permission::ROLE_ADMIN, Permission::ROLE_SUPER),
			),
			'bans' => array(),
			'parents' => array(Permission::ROLE_ADMIN),
		),
	),
);