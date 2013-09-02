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
	 * @param bool $extended
	 * @return array
	 */
	public function flatten($extended = false);

	/**
	 * @return bool
	 */
	public function isNull();
}

