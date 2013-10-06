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
	 *
	 * @ORM\Column(type="string", name="display_name", length=256)
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Attributes({"data-ng-model":"user.displayName"})
	 * @Annotation\Options({"label":"Display Name"})
	 * @Annotation\Filter({"name": "StripTags"})
	 * @Annotation\Filter({"name": "StringTrim"})
	 * @Annotation\Validator({"name": "StringLength", "options": {"min": 1,"max": 256, "messages": {\Zend\Validator\StringLength::INVALID: "Please provide a display name"}}})
	 *
	 * @var string
	 */
	protected $displayName;

	/**
	 *
	 * @ORM\Column(type="string", length=256);
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Attributes({"data-ng-model":"user.email"})
	 * @Annotation\Options({"label":"Email"})
	 * @Annotation\Validator({"name": "EmailAddress", "options": {"messages": {Zend\Validator\EmailAddress::INVALID_FORMAT: "Please provide a valid email address."}}})
	 *
	 * @var string
	 */
	protected $email;

	/**
	 *
	 * @ORM\Column(type="string", name="hash", length=256);
	 * @Annotation\Exclude()
	 *
	 * @var string
	 */
	protected $password;

	/**
	 *
	 * @ORM\Column(type="string", name="login_hash", length=256);
	 * @Annotation\Exclude()
	 *
	 * @var string
	 */
	protected $loginHash;

	/**
	 *
	 * @ORM\Column(type="string", name="name", length=256);
	 * @Annotation\Type("Zend\Form\Element\Text")
	 * @Annotation\Attributes({"data-ng-model":"user.username"})
	 * @Annotation\Options({"label":"User Name"})
	 * @Annotation\Filter({"name": "StripTags"})
	 * @Annotation\Filter({"name": "StringTrim"})
	 * @Annotation\Validator({"name": "StringLength", "options": {"min": 1,"max": 256, "messages": {\Zend\Validator\StringLength::INVALID: "Please provide a user name"}}})
	 *
	 * @var string
	 */
	protected $username;

	/**
	 *
	 * @ORM\Column(type="string", length=8);
	 * @Annotation\Type("Zend\Form\Element\Select")
	 * @Annotation\Options({"label":"State"})
	 * @Annotation\Attributes({"data-ng-model": "user.state",
	 *                          "options":{
	 * 								\Application\Entity\Base\UserInterface::STATE_ACTIVE: "Active",
	 * 								\Application\Entity\Base\UserInterface::STATE_INACTIVE: "Inactive",
	 * 								\Application\Entity\Base\UserInterface::STATE_BANNED: "Banned"
	 * 							}
	 * })
	 * @Annotation\Filter({"name": "StripTags"})
	 * @Annotation\Filter({"name": "StringTrim"})
	 * @Annotation\Validator({"name": "StringLength", "options": {"min": 1,"max": 8}})
	 *
	 * @var string
	 */
	protected $state;

	public function __construct()
	{
		parent::__construct();
		$this->setState(static::STATE_INACTIVE);
	}

	public function flatten($extended = false)
	{
		return array_merge(parent::flatten($extended), array(
			'displayName' => $this->getDisplayName(),
			'email' => $this->getEmail(),
			'username' => $this->getUsername(),
			'state' => $this->getState(),
		));
	}

	public function getDisplayName()
	{
		return $this->displayName;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getId()
	{
		return $this->id();
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

	public function getLoginHash()
	{
		return $this->loginHash;
	}

	public function setLoginHash($hash)
	{
		$this->loginHash = $hash;
		return $this;
	}

}

