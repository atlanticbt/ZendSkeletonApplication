<?php

namespace Application\Service\Search;

use Application\Service\Search;
use Application\Entity\Base\UserInterface as UserEntity;

class User extends Search
{

	protected function _getEntityAlias()
	{
		return 'u';
	}

	protected function _getEntityClass()
	{
		return 'Application\Entity\User';
	}

	protected function _applyFilters(\Doctrine\ORM\QueryBuilder $qb)
	{
		parent::_applyFilters($qb);
		$this->_filterByActive($qb)
				->_filterByName($qb);
		return $this;
	}

	protected function _filterByName(\Doctrine\ORM\QueryBuilder $qb)
	{
		$name = $this->_getParam('name');
		if (!empty($name)) {
			$qb->andWhere('u.name LIKE :name OR u.displayName LIKE :name')
					->setParameter('name', '%' . $name . '%');
		}
		return $this;
	}

	protected function _filterByActive(\Doctrine\ORM\QueryBuilder $qb)
	{
		$qb->andWhere('u.status = :status')
				->setParameter('status', UserEntity::STATE_ACTIVE);
		return $this;
	}

}

