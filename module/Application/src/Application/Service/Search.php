<?php

namespace Application\Service;

use Application\Entity\Base as BaseEntity;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\ArrayCollection;
use Application\Service\PageResponse;

abstract class Search extends BaseService implements SearchInterface
{

	protected $_paginated = true;
	protected $_raw = false;
	protected $_params;
	protected $_singular = false;
	protected $_offset = 0;
	protected $_limit = 10;
	protected $_total = 0;

	/**
	 * The base class for which this service searches.
	 * @return string
	 */
	protected abstract function _getEntityClass();

	/**
	 * The alias of the entityClass in the search query
	 * @return string
	 */
	protected abstract function _getEntityAlias();

	/**
	 * Returns whether the search service is a finder (search by id) vs a searcher (search by criteria)
	 * If $singular is specified, becomes a setter
	 * @param boolean $singular
	 * @return boolean
	 */
	public function singular($singular = null)
	{
		if ($singular === null) {
			return $this->_singular;
		}
		$this->_singular = $singular ? true : false;
		return $this;
	}

	/**
	 * Attach whatever wheres to the class as needed
	 * @return \Application\Service\Search
	 */
	protected function _applyFilters(QueryBuilder $qb)
	{
		return $this;
	}

	/**
	 * Attach whatever order bys to the class as needed
	 * @return \Application\Service\Search
	 */
	protected function _applyOrdering(QueryBuilder $qb)
	{
		$field = $this->_getOrderField();
		if (!empty($field)) {
			$order = 'ASC';
			if ($field{0} == '-') {
				$field = substr($field, 1);
				$order = 'DESC';
			}
			if ($this->_validOrderField($field)) {
				$qb->orderBy($this->_prefixOrderField($field), $order);
			}
		}
		return $this;
	}

	protected function _applyRange(QueryBuilder $qb)
	{
		if ($this->paginated()) {
			$qb->setFirstResult($this->offset())
					->setMaxResults($this->limit());
		}
		return $this;
	}

	protected function _getOrderField()
	{
		return $this->_getParam('order');
	}

	protected function _validOrderField($fieldName)
	{
		return true;
	}

	/**
	 * Generate an order by field like a.street1
	 * @param type $field
	 * @return type
	 */
	protected function _prefixOrderField($field)
	{
		return $this->_getOrderPrefixLookup($field) . '.' . $field;
	}

	/**
	 * For classes which do joins, ordering may be on aliases different from
	 * the entity, so this function will need to be overridden.
	 * @param type $fieldName
	 * @return type
	 */
	protected function _getOrderPrefixLookup($fieldName)
	{
		return $this->_getEntityAlias();
	}

	/**
	 *
	 * @param array $params
	 * @return \Application\Service\Search
	 */
	public function setParams($params)
	{
		$this->_params = $params;
		return $this;
	}

	/**
	 *
	 * @return array
	 */
	protected function _getParams()
	{
		return $this->_params;
	}

	protected function _getParam($index)
	{
		return empty($this->_params) || !isset($this->_params[$index]) ? null : $this->_params[$index];
	}

	protected function _getIdParam()
	{
		return $this->_getParam('id');
	}

	public function setPage($offset, $limit)
	{
		$this->_offset = $offset;
		$this->_limit = $limit;
		return $this;
	}

	public function offset()
	{
		return $this->_offset;
	}

	public function limit()
	{
		return $this->_limit;
	}

	public function total()
	{
		return $this->_total;
	}

	protected function _getRequest()
	{
		if (empty($this->_request)) {
			$this->_request = $this->getServiceLocator()->get('request');
		}
		return $this->_request;
	}

	public function setRequest($request)
	{
		$this->_request = $request;
		return $this;
	}

	/**
	 * @return QueryBuilder
	 */
	protected function _getBaseQueryBuilder()
	{
		return $this->_entityManager()->createQueryBuilder()
						->select($this->_getEntityAlias())
						->from($this->_getEntityClass(), $this->_getEntityAlias());
	}

	protected function _getQuery()
	{
		$qb = $this->_getBaseQueryBuilder();
		$this->_applyFilters($qb)
				->_applyOrdering($qb)
				->_applyRange($qb);
		return $qb->getQuery();
	}

	/**
	 *
	 * @return \IteratorAggregate
	 */
	protected function _doSearch()
	{
		if ($this->singular()) {
			$this->_total = 1;
			return new ArrayCollection(array($this->_entityManager()->find($this->_getEntityClass(), $this->_getIdParam())));
		}
		$paginator = new Paginator($this->_getQuery());
		$this->_total = $paginator->count();
		return $paginator;
	}

	/**
	 * @return \Application\Service\PageResponse
	 */
	protected function _getPageResponse()
	{
		return $this->getServiceLocator()->get('page_response');
	}

	public function firstResult()
	{
		$results = $this->_doSearch();
		$response = $this->_getPageResponse();
		$this->_prepareResponse($results, $response);
		foreach ($results as $item) {
			return $this->raw() ? $item : $this->_appendResult($item, $response);
		}
		return null;
	}

	protected function _prepareResponse($results, PageResponse $pageResponse)
	{
		return $this;
	}

	protected function _appendResult(BaseEntity $result, PageResponse $pageResponse)
	{
		return $result->flatten();
	}

	public function results()
	{
		$results = $this->_doSearch();
		if ($this->raw()) {
			return $results;
		}
		/* @var $pageResponse \Application\Service\PageResponse */
		$pageResponse = $this->_getPageResponse()
				->setOffset($this->offset())
				->setLimit($this->limit())
				->setTotal($this->total());

		$data = array();
		$this->_prepareResponse($results, $pageResponse);
		foreach ($results as $result) {
			$data[] = $this->_appendResult($result, $pageResponse);
		}

		return $this->_finalizeResults($pageResponse
								->setData($data)
								->generate());
	}

	protected function _finalizeResults($viewModelData)
	{
		return $viewModelData;
	}

	protected function _lookup($type)
	{
		/* @var $factory \Application\Factory\Lookup */
		$factory = $this->getServiceLocator()->get('lookup_factory');

		return $factory->configure($type, $this->_getParams())->getService()->lookup();
	}

	public function paginated($paginated = null)
	{
		if ($paginated === null) {
			return $this->_paginated;
		}
		$this->_paginated = $paginated;
		return $this;
	}

	public function raw($raw = null) {
		if ($raw === null) {
			return $this->_raw;
		}
		$this->_raw = $raw;
		return $this;
	}

}

