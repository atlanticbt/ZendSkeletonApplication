<?php

use Application\Service\Permission;
use ZfcUser\Controller\UserController as UserController;

return array(
	'acl_resource_map' => array(
		Permission::ROLE_GUEST => array(
			'permissions' => array(
				'home' => null,
				UserController::ROUTE_LOGIN => null,
				UserController::ROUTE_REGISTER => null,
				UserController::CONTROLLER_NAME . '/logout' => null,
			),
			'bans' => array(),
			'parents' => array(),
		),
		Permission::ROLE_USER => array(
			'permissions' => array(),
			'bans' => array(),
			'parents' => array(Permission::ROLE_GUEST),
		),
		Permission::ROLE_ADMIN => array(
			'permissions' => array(),
			'bans' => array(),
			'parents' => array(Permission::ROLE_USER),
		),
		Permission::ROLE_SUPER => array(
			'permissions' => array(),
			'bans' => array(),
			'parents' => array(Permission::ROLE_ADMIN),
		),
	),
);