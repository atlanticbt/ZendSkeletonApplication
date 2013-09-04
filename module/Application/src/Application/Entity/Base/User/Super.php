<?php

namespace Application\Entity\Base\User;

use Application\Entity\Base\User;
use Application\Service\Permission;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\Entity
 */
class Super extends User
{

	public function getRole()
	{
		return Permission::ROLE_SUPER;
	}

}

