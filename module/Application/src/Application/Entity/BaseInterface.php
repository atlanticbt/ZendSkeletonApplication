<?php

namespace Application\Entity;

interface BaseInterface
{

	/**
	 * Get entity id.
	 * NOTE: CANNOT USE 'getId' because the user entity
	 * implements ZfcUser's interface which also defines a 'getId' method.
	 * Until PHP 5.3.9 you could not have an interface which extends multiple
	 * interfaces with a shared function signature.
	 *
	 * https://bugs.php.net/bug.php?id=46705
	 *
	 *
	 * @return int
	 */
	public function getEId();

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

