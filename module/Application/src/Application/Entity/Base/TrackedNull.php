<?php

namespace Application\Entity\Base;

use Application\Entity\BaseNull;

class TrackedNull extends BaseNull implements TrackedInterface
{

	public function getCreatedTs()
	{
		return new \DateTime();
	}

	public function getLastModifiedTs()
	{
		return new \DateTime();
	}

	public function setCreatedTs($created)
	{
		return $this;
	}

	public function setLastModifiedTs($lastModified)
	{
		return $this;
	}

}

