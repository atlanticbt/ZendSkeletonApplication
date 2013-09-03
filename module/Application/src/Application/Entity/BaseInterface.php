<?php

namespace Application\Entity;

interface BaseInterface
{

	/**
	 * @return int
	 */
	public function getId();

	/**
	 *
	 * @param boolean $extended
	 * @return array
	 */
	public function flatten($extended = false);

	/**
	 * @return boolean
	 */
	public function isNull();
}

