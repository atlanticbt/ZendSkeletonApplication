<?php

namespace Application\Service;

use Application\Entity\BaseInterface;
use Application\Service\Lookup\Exception\FailedLookup as FailedLookupException;

abstract class Lookup extends BaseService implements LookupInterface
{

	protected $_params;

	public function lookup()
	{
		$entity = $this->_getEntity();
		if (!$this->_verify($entity)) {
			throw new FailedLookupException();
		}
		return $entity;
	}

	/**
	 *
	 * @return BaseInterface
	 * @throws FailedLookupException
	 */
	protected function _getEntity()
	{
		foreach ($this->_getLookupMethods() as $callable) {
			$result = call_user_func($callable);
			if ($this->_lookupSucceeded($result)) {
				return $result;
			}
		}
		throw new FailedLookupException();
	}

	/**
	 * Returns an array of callables. Starting with the first, will try each
	 * method until one succeeds
	 * @return type
	 */
	protected function _getLookupMethods()
	{
		return array(
			array($this, '_defaultLookup'),
		);
	}

	/**
	 *
	 * @return BaseInterface
	 */
	protected function _defaultLookup()
	{
		return $this->_emlookup($this->_getEntityClass(), $this->_getEntityId());
	}

	/**
	 * Determines if a result from _getLookupMethods callable successfully looked up an entity.
	 * @param type $result
	 */
	protected function _lookupSucceeded($result)
	{
		return !empty($result) && ($result instanceof BaseInterface) && !$result->isNull();
	}

	/**
	 * After a lookup method succeeded, this verifies any final requirements
	 * before returning the entity.
	 * @param \Application\Entity\BaseInterface $entity
	 * @return boolean
	 */
	protected function _verify(BaseInterface $entity)
	{
		return true;
	}

	/**
	 * @return string
	 */
	protected abstract function _getEntityClass();

	/**
	 * @return int|null
	 */
	protected abstract function _getEntityId();

	public function setParams(array $params)
	{
		$this->_params = $params;
		return $this;
	}

	/**
	 *
	 * @param string $index
	 * @return mixed
	 */
	protected function _getParam($index)
	{
		return isset($this->_params[$index]) ? $this->_params[$index] : null;
	}

}

