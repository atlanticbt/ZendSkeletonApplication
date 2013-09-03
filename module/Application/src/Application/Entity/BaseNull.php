<?php

namespace Application\Entity;

abstract class BaseNull implements BaseInterface
{

	public function flatten($extended = false)
	{
		return array();
	}

	public function getId()
	{
		return null;
	}

	public function isNull()
	{
		return true;
	}

}

