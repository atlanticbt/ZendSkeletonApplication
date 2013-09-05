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

	protected function _getEntity()
	{
		$class = $this->_getEntityClass();
		$id = $this->_getEntityIdParam();
		if (empty($class) || empty($id)) {
			return null;
		}
		return $this->_entityManager()->find($class, $id);
	}

}

