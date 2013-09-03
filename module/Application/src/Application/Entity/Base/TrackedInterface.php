<?php

namespace Application\Entity\Base;

use Application\Entity\BaseInterface;

interface TrackedInterface extends BaseInterface
{

	/**
	 * @return \DateTime
	 */
	public function getCreatedTs();

	/**
	 * Accepts either string or \DateTime object
	 * @param mixed $created
	 * @return TrackedInterface
	 */
	public function setCreatedTs($created);

	/**
	 * @return \DateTime
	 */
	public function getLastModifiedTs();

	/**
	 * Accepts either string or \DateTime object
	 * @param mixed $lastModified
	 * @return TrackedInterface
	 */
	public function setLastModifiedTs($lastModified);
}

