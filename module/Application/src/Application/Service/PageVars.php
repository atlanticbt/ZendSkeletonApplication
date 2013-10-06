<?php

namespace Application\Service;

use Zend\Http\Request as HttpRequest;
use Zend\Console\Request as ConsoleRequest;
use Zend\Stdlib\RequestInterface as Request;

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
		if ($request instanceof HttpRequest) {
			$this->_httpRequest($request);
		} else if ($request instanceof ConsoleRequest) {
			$this->_consoleRequest($request);
		}
		if (!is_numeric($this->_offset) || empty($this->_offset)) {
			$this->_offset = 0;
		}
		if (!is_numeric($this->_limit) || empty($this->_limit)) {
			$this->_limit = 10;
		}
		return $this;
	}

	protected function _httpRequest(HttpRequest $request) {
		$this->_offset = (int) ($request->getPost('offset') ? : $request->getQuery('offset'));
		$this->_limit = (int) ($request->getPost('limit') ? : $request->getQuery('limit'));
		return $this;
	}

	protected function _consoleRequest(ConsoleRequest $request) {
		$this->_offset = $request->getParam('offset');
		$this->_limit = $request->getParam('limit');
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

