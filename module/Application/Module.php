<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{

	public function onBootstrap(MvcEvent $e)
	{
		$eventManager = $e->getApplication()->getEventManager();
		$moduleRouteListener = new ModuleRouteListener();
		$moduleRouteListener->attach($eventManager);
		$serviceManager = $e->getApplication()->getServiceManager();
		// enforcing ACL in routing.
		$e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, array($serviceManager->get('route_service'), 'onRoute'));
		// listen for user creation, send invite
		/* @var $createAccount \Application\Service\CreateAccountListener */
		$serviceManager->get('create_account_service')->latch();

		$serviceManager->get('listener_account')->latch();

		$serviceManager->get('listener_auditor')->latch();
	}

	public function getConfig()
	{
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig()
	{
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

	/**
	 * Returns a string containing a banner text, that describes the module and/or the application.
	 * The banner is shown in the console window, when the user supplies invalid command-line parameters or invokes
	 * the application with no parameters.
	 *
	 * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access Console and send
	 * output.
	 *
	 * @param AdapterInterface $console
	 * @return string|null
	 */
	public function getConsoleBanner(AdapterInterface $console)
	{
		return "------ ABT ZF2 ------";
	}

	/**
	 * Returns an array or a string containing usage information for this module's Console commands.
	 * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access
	 * Console and send output.
	 *
	 * If the result is a string it will be shown directly in the console window.
	 * If the result is an array, its contents will be formatted to console window width. The array must
	 * have the following format:
	 *
	 *     return array(
	 *                'Usage information line that should be shown as-is',
	 *                'Another line of usage info',
	 *
	 *                '--parameter'        =>   'A short description of that parameter',
	 *                '-another-parameter' =>   'A short description of another parameter',
	 *                ...
	 *            )
	 *
	 * @param AdapterInterface $console
	 * @return array|string|null
	 */
	public function getConsoleUsage(AdapterInterface $console)
	{
		return array(
			'run cron' => 'Runs the cron.',
		);
	}
}
