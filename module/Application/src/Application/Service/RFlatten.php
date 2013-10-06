<?php
namespace Application\Service;
use Application\Entity\Base;


/*
 * service: rflatten
 */
class RFlatten extends BaseService {

	public function __invoke($data)
	{
		return $this->_recursiveFlatten($data);
	}

	/**
	 * Take a Doctrine entity which implements the base interface (has a flatten function)
	 * and invokes flatten on it. Else checks if it's an array. If so, inspects all data
	 * @param type $data
	 * @return \Application\View\Helper\Traversable
	 */
	protected function _recursiveFlatten($data) {
		if (is_object($data) && $data instanceof Base) {
			return $data->flatten();
		}
		if (is_array($data) || $data instanceof \Traversable) {
			$flatArray = array();
			foreach ($data as $key => $value) {
				$flatArray[$key] = $this->_recursiveFlatten($value);
			}
			return $flatArray;
		}
		return $data;
	}

}