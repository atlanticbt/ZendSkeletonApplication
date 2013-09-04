<?php

namespace Application\Service;

/**
 * service: page_response
 */
class PageResponse extends BaseService
{

	private $_shared;
	private $_offset;
	private $_total;
	private $_limit;
	private $_data = null;
	private $_options = null;

	public function setOffset($offset)
	{
		$this->_offset = $offset;
		return $this;
	}

	public function setTotal($total)
	{
		$this->_total = $total;
		return $this;
	}

	public function setLimit($limit)
	{
		$this->_limit = $limit;
		return $this;
	}

	public function setData($data)
	{
		$this->_data = $data;
		return $this;
	}

	public function setOptions($options)
	{
		$this->_options = $options;
		return $this;
	}

	public function setShared($sharedData)
	{
		$this->_shared = $sharedData;
		return $this;
	}

	public function setOption($key, $value)
	{
		return $this->appendOption($key, $value, false);
	}

	public function appendOption($key, $value, $index = null)
	{
		if (empty($this->_options)) {
			$this->_options = array();
		}
		if (empty($this->_options[$key])) {
			$this->_options[$key] = array();
		}
		if ($index === null) {
			$this->_options[$key][] = $value;
		} else if ($index === false) {
			$this->_options[$key] = $value;
		} else {
			$this->_options[$key][$index] = $value;
		}
		return $this;
	}

	public function appendShared($key, $value, $index = null)
	{
		if (empty($this->_shared)) {
			$this->_shared = array();
		}
		if (empty($this->_shared[$key])) {
			$this->_shared[$key] = array();
		}
		if ($index === null) {
			$this->_shared[$key][] = $value;
		} else {
			$this->_shared[$key][$index] = $value;
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function generate()
	{
		return array('page' => array(
				'data' => $this->_data,
				'options' => $this->_options,
				'shared' => $this->_shared,
				'meta' => array(
					'total' => $this->_total,
					'offset' => $this->_offset,
					'limit' => $this->_limit,
				),
			),
		);
	}

}
