<?php

namespace Application\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\AbstractHelper;
use Application\Entity\Base;

/**
 * @author John F <john.foushee@atlanticbt.com>
 * @company Atlantic Business Technology
 *
 * View helper for injecting data from the controller into the view for Angular to consume
 *
 */
class InitNg extends AbstractHelper implements ServiceLocatorAwareInterface
{
	protected $_helperManager;

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

	public function recursiveFlatten($data)
	{
		$flattener = $this->getServiceLocator()->getServiceLocator()->get('rflatten');
		return $flattener($data);
	}

	/**
	 * Set service locator
	 *
	 * @param ServiceLocatorInterface $serviceLocator
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->_helperManager = $serviceLocator;
		return $this;
	}

	/**
	 * Get service locator
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->_helperManager;
	}
}

