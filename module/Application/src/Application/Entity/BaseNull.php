<?php

namespace Application\Entity;

abstract class BaseNull implements BaseInterface
{

	public function flatten($extended = false)
	{
		return array();
	}

	public function getEId()
	{
		return null;
	}

	public function isNull()
	{
		return true;
	}

}

