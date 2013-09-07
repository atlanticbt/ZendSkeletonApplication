<?php

namespace Application\Factory\Update;

use Application\Factory\BaseFactory;
use Application\Service\UpdateInterface;

abstract class UpdateFactory extends BaseFactory implements UpdateFactoryInterface
{

	/**
	 *
	 * @return UpdateInterface
	 */
	public final function getService()
	{
		/* @var $service UpdateInterface */
		$service = $this->_getUpdateService();
		$this->_setServiceParams($service);
		$this->_setServiceEntity($service);
		return $service;
	}

	/**
	 * @return UpdateInterface
	 */
	protected abstract function _getUpdateService();

	protected function _setServiceParams(UpdateInterface $service)
	{
		$service->setParams($this->_getParams());
	}

	protected function _setServiceEntity(UpdateInterface $service)
	{
		$entity = $this->_getEntity();
		if (!empty($entity)) {
			$service->setEntity($entity);
		}
	}

	protected function _getEntityClass()
	{
		return null;
	}

	protected function _getEntityIdParam()
	{
		return $this->_getParam('id');
	}

	/**
	 * Attempts to determine if the current request is updating an existing entity
	 * based on the return values of the _getEntityClass and _getEntityIdParam methods.
	 * @return null|\Application\Entity\Base
	 */
	protected function _getEntity()
	{
		$class = $this->_getEntityClass();
		$id = $this->_getEntityIdParam();
		if (empty($class) || empty($id)) {
			return null;
		}
		return $this->_entityManager()->find($class, $id);
	}

	/**
	 * Instantiates and persists a new entity based on the class returned by _getEntityClass
	 * Automatically persists the entity.
	 * @return \Application\Factory\Update\class
	 * @throws \InvalidArgumentException
	 */
	protected function _getNewEntity()
	{
		$class = '\\' . $this->_getEntityClass();
		if (!class_exists($class)) {
			throw new \InvalidArgumentException('No entity of class "' . $class . '" available for instatiation.');
		}
		$entity = new $class();
		$this->_entityManager()->persist($entity);
		return $entity;
	}

}

