<?php

namespace Application\Factory\Search;

use Application\Factory\BaseFactory;
use Application\Service\SearchInterface;

abstract class SearchFactory extends BaseFactory implements SearchFactoryInterface
{

	/**
	 *
	 * @return SearchInterface
	 */
	public final function getService()
	{
		/* @var $service SearchInterface */
		$service = $this->_getSearchService();
		$this->_setServiceParams($service);
		$this->_setPage($service);
		return $service;
	}

	/**
	 * @return SearchInterface
	 */
	protected abstract function _getSearchService();

	protected function _setServiceParams(SearchInterface $service)
	{
		$service->setParams($this->_getParams());
		return $this;
	}

	protected function _setPage(SearchInterface $service)
	{
		/* @var $pageVarService \Application\Service\PageVars */
		$pageVarService = $this->getServiceLocator()->get('page_vars');
		$pageVarService->seed($this->_getRequest());
		$service->setPage($pageVarService->offset(), $pageVarService->limit());
		return $this;
	}

}

