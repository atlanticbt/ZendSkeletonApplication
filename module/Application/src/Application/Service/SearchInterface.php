<?php

namespace Application\Service;

interface SearchInterface
{

	/**
	 * Returns whether the search service is a finder (search by id) vs a searcher (search by criteria)
	 * @return boolean
	 */
	public function singular($singular = null);

	/**
	 * Returns whether the search service is paginated. If $paginated is not null,
	 * it sets the paginated state of the service.
	 * @param type $paginated
	 * @return SearchInterface
	 */
	public function paginated($paginated = null);

	/**
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return SearchInterface
	 */
	public function setPage($offset, $limit);

	/**
	 * Set search request parameters, typically injected by search factory.
	 * @param array $params
	 * @return SearchInterface
	 */
	public function setParams($params);

	/**
	 * @return int
	 */
	public function offset();

	/**
	 * @return int
	 */
	public function limit();

	/**
	 *
	 * @param type $request
	 * @return SearchInterface
	 */
	public function setRequest($request);

	/**
	 * Returns entity if singular, else collection of entities
	 *
	 * @return \Traversable
	 */
	public function results();

	/**
	 * Returns first search result (or null if none)
	 *
	 * @return \Application\Entity\Base
	 */
	public function firstResult();
}

