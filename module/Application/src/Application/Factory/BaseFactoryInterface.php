<?php

namespace Application\Factory;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\EventManager\EventManagerAwareInterface;
use Application\Service\BaseService;
use Zend\Stdlib\RequestInterface as Request;

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

	/**
	 *
	 * @param array $params
	 * @return BaseFactoryInterface
	 */
	public function setParams(array $params);
}

