<?php

use ZfcUser\Controller\UserController;
use Application\Controller\UserController as ManageUserController;

return array(
// ...
	'navigation' => array(
		'default' => array(
			'guest-top-nav' => array(
				'label' => 'guest-top-nav',
				'id' => 'guest-top-nav',
				'uri' => '/',
				'pages' => array(
				),
			),
			'top-nav' => array(
				'label' => 'top-nav',
				'id' => 'top-nav',
				'uri' => '/',
				'pages' => array(
					array(
						'label' => 'Log In',
						'route' => UserController::ROUTE_LOGIN,
						'resource' => UserController::ROUTE_LOGIN,
					),
					array(
						'label' => 'Register',
						'route' => UserController::ROUTE_REGISTER,
						'resource' => UserController::ROUTE_REGISTER,
					),
					array(
						'label' => 'Change Email',
						'route' => UserController::ROUTE_CHANGEEMAIL,
						'resource' => UserController::ROUTE_CHANGEEMAIL,
					),
					array(
						'label' => 'Change Password',
						'route' => UserController::ROUTE_CHANGEPASSWD,
						'resource' => UserController::ROUTE_CHANGEPASSWD,
					),
					array(
						'label' => 'Manage Users',
						'route' => ManageUserController::ROUTE_USER_MANAGE,
						'resource' => ManageUserController::ROUTE_USER_MANAGE,
					),
					array(
						'label' => 'Log Out',
						'route' => UserController::CONTROLLER_NAME . '/logout',
						'resource' => UserController::CONTROLLER_NAME . '/logout',
					),
				),
			),
		),
	),
);
