<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

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

	/**
	 *
	 * @return EntityManager
	 */
	protected function _entityManager()
	{
		return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
	}

	/**
	 *
	 * @param string $className
	 * @param int $id
	 * @return \Application\Entity\BaseInterface
	 */
	protected function _emlookup($className, $id)
	{
		$entity = !empty($id) ? $this->_entityManager()->find($className, $id) : null;
		$nullClass = $className . 'Null';
		if ($className{0} != '\\') {
			$nullClass = '\\' . $nullClass;
		}
		if (empty($entity) && class_exists($nullClass)) {
			$entity = new $nullClass();
		}
		return $entity;
	}

}

