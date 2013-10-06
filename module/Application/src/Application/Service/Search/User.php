<?php

namespace Application\Service\Search;

use Application\Service\PageResponse;
use Application\Service\Search;
use Application\Entity\Base\UserInterface as UserEntity;
use Doctrine\ORM\QueryBuilder;
use Application\Factory\Role as RoleFactory;
use Application\Service\Lookup\Exception\FailedLookup as FailedLookupException;

/**
 * service: user_search_service
 */
class User extends Search
{

	protected function _getEntityAlias()
	{
		return 'u';
	}

	protected function _getEntityClass()
	{
		return '\Application\Entity\Base\User';
	}

	protected function _applyFilters(\Doctrine\ORM\QueryBuilder $qb)
	{
		parent::_applyFilters($qb);
		$this->_filterByActive($qb)
				->_filterByName($qb)
				->_filterByEmail($qb)
				->_filterByRole($qb);
		return $this;
	}

	protected function _filterByName(\Doctrine\ORM\QueryBuilder $qb)
	{
		$name = $this->_getParam('name');
		if (!empty($name)) {
			$qb->andWhere('u.displayName LIKE :name OR u.username LIKE :name')
					->setParameter('name', '%' . $name . '%');
		}
		return $this;
	}

	protected function _filterByEmail(\Doctrine\ORM\QueryBuilder $qb)
	{
		$email = $this->_getParam('email');
		if (!empty($email)) {
			$qb->andWhere('u.email LIKE :email')
					->setParameter('email', '%' . $email . '%');
		}
		return $this;
	}

	protected function _filterByActive(\Doctrine\ORM\QueryBuilder $qb)
	{
		$qb->andWhere('u.state = :state')
				->setParameter('state', UserEntity::STATE_ACTIVE);
		return $this;
	}

	protected function _filterByRole(QueryBuilder $qb)
	{
		$role = $this->_getParam('withRole');
		$validRoles = $this->_getValidRoles();
		if (!empty($role) && (empty($validRoles) || in_array($role, $validRoles))) {
			$validRoles = array($role);
		}
		if (!empty($validRoles)) {
			// get role factory
			$roleFactory = new RoleFactory();
			$roleWhere = array();
			foreach ($validRoles as $validRole) {
				$roleWhere[] = 'u INSTANCE OF ' . $roleFactory->getClassFromRole($validRole);
			}
			$qb->andWhere(implode(' OR ', $roleWhere));
		}
		return $this;
	}

	protected function _getValidRoles()
	{
		return array();
	}

	protected function _getIdParam()
	{
		return $this->_getParam('user');
	}

	protected function isTypeahead()
	{
		$typeahead = $this->_getParam('typeahead');
		return !empty($typeahead);
	}

}

