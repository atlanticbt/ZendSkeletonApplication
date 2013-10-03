<?php

namespace Application\Entity\Base;

use Application\Entity\Base\TrackedNull;
use Application\Service\Permission;

class UserNull extends TrackedNull implements UserInterface
{

	public function getDisplayName()
	{
		return null;
	}

	public function getEmail()
	{
		return null;
	}

	public function getId()
	{
		return $this->id();
	}

	public function getPassword()
	{
		return null;
	}

	public function getState()
	{
		return null;
	}

	public function getUsername()
	{
		return null;
	}

	public function setDisplayName($displayName)
	{
		return $this;
	}

	public function setEmail($email)
	{
		return $this;
	}

	public function setId($id)
	{
		return $this;
	}

	public function setPassword($password)
	{
		return $this;
	}

	public function setState($state)
	{
		return $this;
	}

	public function setUsername($username)
	{
		return $this;
	}

	public function getRole()
	{
		return Permission::ROLE_GUEST;
	}

	public function getLoginHash()
	{
		return null;
	}

	public function setLoginHash($hash)
	{
		return $this;
	}

}

