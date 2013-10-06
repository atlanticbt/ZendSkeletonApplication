<?php

namespace Application\Service;

interface LookupInterface
{

	/**
	 *
	 * @param array $params
	 * @return LookupInterface
	 */
	public function setParams(array $params);

	/**
	 *
	 * @return BaseInterface
	 * @throws FailedLookupException
	 */
	public function lookup();
}

