<?php

namespace Application\Entity;

use Doctrine\ORM\EntityManager;

abstract class BaseNull implements BaseInterface
{

	public function flatten($extended = false)
	{
		return array();
	}

	public function id()
	{
		return null;
	}

	public function isNull()
	{
		return true;
	}

	public function __toString() {
		return '';
	}

}

