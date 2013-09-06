<?php

namespace Application\Factory;

use Application\Service\BaseService;
use Zend\Http\Request;

abstract class BaseFactory extends BaseService implements BaseFactoryInterface
{

	protected $_eventManager;
	protected $_routeParams;
	protected $_request;
	protected $_params;

	/**
	 *
	 * @return Request
	 */
	protected function _getRequest()
	{
		if (empty($this->_request)) {
			$this->_request = $this->getServiceLocator()->get('request');
		}
		return $this->_request;
	}

	/**
	 *
	 * @param \Zend\Http\Request $request
	 */
	public function setRequest(Request $request)
	{
		$this->_request = $request;
		return $this;
	}

	/**
	 * Returns routeParams as set. If none were set, matches route to the request
	 * and returns any params matched.
	 * @return array
	 */
	protected function _getRouteParams()
	{
		if (!isset($this->_routeParams)) {

			/* @var $route \Zend\Mvc\Router\RouteInterface */
			$route = $this->getServiceLocator()->get('router');
			/* @var $match \Zend\Mvc\Router\RouteMatch */
			$match = $route->match($this->_getRequest());
			$params = $match->getParams();
			$this->_routeParams = empty($params) ? array() : $params;
		}
		return $this->_routeParams;
	}

	protected function _getParams()
	{
		if (!isset($this->_params)) {
			$this->_params = array_merge($this->_getRouteParams(), $this->_getRequest()->getPost()->toArray());
		}
		return $this->_params;
	}

	protected function _getParam($index)
	{
		$params = $this->_getParams();
		return isset($params[$index]) ? $params[$index] : null;
	}

	public function getEventManager()
	{
		return $this->_eventManager;
	}

	public function setEventManager(\Zend\EventManager\EventManagerInterface $eventManager)
	{
		$this->_eventManager = $eventManager;
		return $this;
	}

}

