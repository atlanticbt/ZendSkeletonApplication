<?php

namespace Application\Entity\Base;

use Application\Entity\Base\Tracked;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Application\Service\Permission;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="role", type="string")
 * @ORM\DiscriminatorMap({"member" = "Application\Entity\Base\User","admin" = "Application\Entity\Base\User\Admin","super" = "Application\Entity\Base\User\Super"})
 *
 *
 * @ORM\Table(name="user")
 */
class User extends Tracked implements UserInterface
{

	/**
	 * @ORM\Column(type="string", name="display_name", length=256);
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Options({"label":"Display Name"})
	 *
	 * @var type
	 */
	protected $displayName;

	/**
	 * @ORM\Column(type="string", length=256);
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Options({"label":"Email"})
	 *
	 * @var type
	 */
	protected $email;

	/**
	 * @ORM\Column(type="string", name="hash", length=256);
	 * @Annotation\Type("Zend\Form\Element\Password")
	 * @Annotation\Options({"label":"Password"})
	 *
	 * @var type
	 */
	protected $password;

	/**
	 * @ORM\Column(type="string", name="name", length=256);
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Options({"label":"User Name"})
	 *
	 * @var type
	 */
	protected $username;

	/**
	 * @ORM\Column(type="string", length=8);
	 * @Annotation\Exclude()
	 *
	 * @var type
	 */
	protected $state;

	public function __construct()
	{
		parent::__construct();
		$this->setState(static::STATE_INACTIVE);
	}

	public function getDisplayName()
	{
		return $this->displayName;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getState()
	{
		return $this->state;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
		return $this;
	}

	public function setEmail($email)
	{
		$this->email = $email;
		return $this;
	}

	public function setId($id)
	{
		return $this;
	}

	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}

	public function setUsername($username)
	{
		$this->username = $username;
		return $this;
	}

	public function getRole()
	{
		return Permission::ROLE_USER;
	}

}

