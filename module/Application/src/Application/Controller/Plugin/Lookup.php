<?php

namespace Application\Controller\Plugin;

/**
 * plugin: lookup
 */
class Lookup extends BasePlugin {

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
			$this->_params = array_merge($this->_getRouteParams(), $this->_getRequest()->getQuery()->toArray(), $this->_getRequest()->getPost()->toArray());
		}
		return $this->_params;
	}
	public function __invoke($type) {
		return $this->getServiceLocator()->get('lookup_factory')->configure($type, $this->_getParams())->getService()->lookup();
	}
}