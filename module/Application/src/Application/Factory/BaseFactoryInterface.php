<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Application\Service\BaseService;
use Zend\Http\Request;

interface BaseFactoryInterface extends ServiceLocatorAwareInterface, EventManagerAwareInterface
{

	/**
	 * @return BaseService
	 * @throws \Zend\ServiceManager\Exception\ServiceNotFoundException
	 */
	public function getService();

	/**
	 *
	 * @param \Zend\Http\Request $request
	 * @return BaseFactoryInterface
	 */
	public function setRequest(Request $request);
}

