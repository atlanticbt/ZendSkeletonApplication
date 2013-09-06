<?php

namespace Application\Factory\Search;

use Application\Factory\Search\SearchFactory;

/**
 * service: user_search_factory
 */
class User extends SearchFactory
{

	/**
	 * Returns and sets up the user search service. If a 'user' param is set
	 * in either the route or the POST data, it becomes a singular search
	 * service which means it looks up a user by that id.
	 * @return type
	 */
	protected function _getSearchService()
	{
		$userIdParam = $this->_getParam('user');
		return $this->getServiceLocator()->get('user_search_service')->singular(!empty($userIdParam));
	}

}

