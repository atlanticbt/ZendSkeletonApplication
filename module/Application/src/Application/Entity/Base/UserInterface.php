<?php

namespace Application\Entity\Base;

use Application\Entity\Base\TrackedInterface;
use ZfcUser\Entity\UserInterface as ZfcUserInterface;

interface UserInterface extends TrackedInterface, ZfcUserInterface
{

	const STATE_INACTIVE = 'inactive';
	const STATE_ACTIVE = 'active';
	const STATE_BANNED = 'banned';

	/**
	 * @return string
	 */
	public function getRole();
}

