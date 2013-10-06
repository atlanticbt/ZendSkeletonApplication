<?php

namespace Application\Factory;

/**
 * service: lookup_factory
 */
class Lookup extends BaseFactory
{

	protected $_type;
	protected $_params;

	public function configure($type, $params)
	{
		$this->_type = $type;
		$this->_params = $params;
		return $this;
	}

	/**
	 *
	 * @return \Application\Service\LookupInterface
	 */
	public function getService()
	{
		/* @var $lookupService \Application\Service\LookupInterface */
		$lookupService = $this->getServiceLocator()->get('lookup_' . $this->_type);
		$lookupService->setParams($this->_params);

		return $lookupService;
	}

}

