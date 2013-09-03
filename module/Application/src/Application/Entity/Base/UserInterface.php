<?php

namespace Application\Entity\Base;

use Application\Entity\BaseInterface;
use ZfcUser\Entity\UserInterface as ZfcUserInterface;

interface UserInterface extends BaseInterface, ZfcUserInterface
{

	const STATE_INACTIVE = 'inactive';
	const STATE_ACTIVE = 'active';
	const STATE_BANNED = 'banned';

	public function getRole();
}

