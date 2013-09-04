<?php

namespace Application\Service;

use Zend\Http\Request;

/**
 * service: page_vars
 * Service for determining the page of a search request.
 */
class PageVars
{

	protected $_offset;
	protected $_limit;

	public function seed(Request $request)
	{
		$offset = (int) ($request->getPost('offset') ? : $request->getQuery('offset'));
		$limit = (int) ($request->getPost('limit') ? : $request->getQuery('limit'));
		if (!is_numeric($offset) || empty($offset)) {
			$offset = 0;
		}
		if (!is_numeric($limit) || empty($limit)) {
			$limit = 10;
		}
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

}

