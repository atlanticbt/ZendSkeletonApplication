<?php

namespace Application\View\Helper;

use ZfcUser\View\Helper\ZfcUserIdentity;

class UserIdentity extends ZfcUserIdentity
{

	public function __invoke()
	{
		return $this->getAuthService()->getIdentity();
	}

}

