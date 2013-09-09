<?php

namespace DocurepAdmin\Service\Invitation;

use DocurepAdmin\Service\Invitation;

class Single extends Invitation
{

	public function process()
	{
		if ($this->permitted($this->getRole())) {
			return $this->create($this->getRequest()->getPost())->send($this->getInvitedUser())->success();
		}
		return false;
	}

}

