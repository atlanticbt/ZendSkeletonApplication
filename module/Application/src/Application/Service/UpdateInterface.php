<?php

namespace Application\Service;

use Application\Entity\Base;

interface UpdateInterface
{

	/**
	 * Typically set by the UpdateFactory which determines what entity is
	 * being updated.
	 *
	 * @param \Application\Entity\Base $entity
	 * @return UpdateInterface
	 */
	public function setEntity(Base $entity);

	/**
	 * Typically set by UpdateFactory to inject whatever data is needed to
	 * update the entity.
	 *
	 * @param type $params
	 * @return UpdateInterface
	 */
	public function setParams($params);

	/**
	 * Typically called when displaying a page to update an entity.
	 * @return \Zend\Form\Form
	 */
	public function form($type = null);

	/**
	 * Execute the update.
	 *
	 * @return UpdateInterface
	 */
	public function update();

	/**
	 * @return bool
	 */
	public function success();

	/**
	 * @return mixed
	 */
	public function message();

	/**
	 * @return array
	 */
	public function responseData();
}

