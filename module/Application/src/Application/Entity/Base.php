<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\MappedSuperclass
 */
abstract class Base implements BaseInterface
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer");
	 * @ORM\GeneratedValue(strategy="AUTO")
	 *
	 * @Annotation\Exclude()
	 *
	 * @var int
	 */
	protected $id;

	public function __construct()
	{

	}

	public function flatten($extended = false)
	{
		return array('id' => $this->getId());
	}

	public function getId()
	{
		return $this->id;
	}

	public function isNull()
	{
		return false;
	}

}

