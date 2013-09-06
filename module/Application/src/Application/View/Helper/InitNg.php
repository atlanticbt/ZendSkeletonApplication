<?php

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Entity\Base;

/**
 * @author John F <john.foushee@atlanticbt.com>
 * @company Atlantic Business Technology
 *
 * View helper for injecting data from the controller into the view for Angular to consume
 *
 */
class InitNg extends AbstractHelper
{

	/**
	 *
	 * @param string $name Name of variable or function
	 * @param mixed $raw Data to be assigned/passed to $name
	 * @param boolean $setAsFunction flag to indicate whether data should be assigned or passed as parameter
	 * @param boolean $attemptToFlatten flag to indicate data should be recursively iterated over, flattening any Base entity classes in the process
	 * @return string
	 */
	public function __invoke($name, $raw, $setAsFunction = false, $attemptToFlatten = false)
	{
		$data = $attemptToFlatten ? $this->recursiveFlatten($raw) : $raw;
		$initData = htmlspecialchars(json_encode($data), ENT_COMPAT);
		return ' ng-init="' . $name . ($setAsFunction ? "({$initData})" : " = $initData") . ';" ';
	}

	/**
	 * Take a Doctrine entity which implements the base interface (has a flatten function)
	 * and invokes flatten on it. Else checks if it's an array. If so, inspects all data
	 * @param type $data
	 * @return \Application\View\Helper\Traversable
	 */
	public function recursiveFlatten($data)
	{
		if (is_object($data) && $data instanceof Base) {
			return $data->flatten();
		}
		if (is_array($data) || $data instanceof \Traversable) {
			$flatArray = array();
			foreach ($data as $key => $value) {
				$flatArray[$key] = $this->recursiveFlatten($value);
			}
			return $flatArray;
		}
		return $data;
	}

}

