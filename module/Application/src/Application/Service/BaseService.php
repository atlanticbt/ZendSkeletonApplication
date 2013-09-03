<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class BaseService implements ServiceLocatorAwareInterface
{

	/**
	 *
	 * @var ServiceLocatorInterface
	 */
	private $_locator;

	/**
	 *
	 * @return ServiceLocatorInterface
	 */
	public function getServiceLocator()
	{
		return $this->_locator;
	}

	/**
	 *
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
	 * @return \Application\Service\BaseService
	 */
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->_locator = $serviceLocator;
		return $this;
	}

	/**
	 * @return \Application\Entity\Base\User
	 */
	protected function _currentUser()
	{
		return $this->getServiceLocator()->get('application_auth_service')->getIdentity();
	}

	protected function _entityManager()
	{
		return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	}

}

