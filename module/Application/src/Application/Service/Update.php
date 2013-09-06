<?php

namespace Application\Service;

use Application\Entity\Base;
use Application\Service\EntityToForm;
use Zend\Form\Form;
use Application\Service\Update\Exception\UnsetEntity as UnsetEntityException;

class Update extends BaseService implements UpdateInterface
{

	const BASE_FORM_NAME = 'entity';

	/**
	 *
	 * @var Base
	 */
	protected $_entity;

	/**
	 *
	 * @var array
	 */
	protected $_params;

	/**
	 *
	 * @var array
	 */
	protected $_forms;

	/**
	 *
	 * @var bool
	 */
	protected $_success;

	/**
	 *
	 * @var mixed
	 */
	protected $_message;

	/**
	 *
	 * @var type
	 */
	protected $_validationGroup = array();

	/**
	 *
	 * @param \Application\Entity\Base $entity
	 * @return \Application\Service\Update
	 */
	public function setEntity(Base $entity)
	{
		$this->_entity = $entity;
		return $this;
	}

	/**
	 * Update factory usually sets the data available to the updater. (POST/Route data)
	 * @param type $params
	 * @return \Application\Service\Update
	 */
	public function setParams($params)
	{
		$this->_params = $params;
		return $this;
	}

	/**
	 * Returns the base entity
	 * @return Base
	 */
	public function entity()
	{
		return $this->_getEntity();
	}

	/**
	 * Returns the entity-converted form of the specified type
	 * (leave blank for default entity form)
	 * @param string|null $type
	 * @return \Zend\Form\Form
	 */
	public function form($type = null)
	{
		$forms = $this->_getForms();
		if (empty($type) || !isset($forms[$type])) {
			$type = static::BASE_FORM_NAME;
		}
		return $forms[$type];
	}

	/**
	 * Attempts to update the entities.
	 * @return \Application\Service\Update
	 */
	public function update()
	{
		$this->_prepare();
		$this->_validate();
		$this->_postValidate();
		return $this;
	}

	/**
	 * Returns whether the entity was updated.
	 * @return bool
	 */
	public function success()
	{
		return $this->_success;
	}

	/**
	 * Returns the response message of the update attempt
	 * @return mixed (could be string or array of error messages)
	 */
	public function message()
	{
		return $this->_message;
	}

	/**
	 * Any extra data to be returned in a JSON response to the update
	 * @return type
	 */
	public function responseData()
	{
		return $this->success() ? $this->_successData() : $this->_failData();
	}

	/**
	 * Data to be returned in the event of a success.
	 * @return type
	 */
	protected function _successData()
	{
		return array();
	}

	/**
	 * Data to be returned in the event of a failure.
	 * @return type
	 */
	protected function _failData()
	{
		return array();
	}

	/**
	 * Sets up whatever is needed to validate the form
	 */
	protected function _prepare()
	{
		$this->_forms = array();
		foreach ($this->_getForms() as $formTag => $form) {
			if ($this->_hasValidationGroup($formTag)) {
				$form->setValidationGroup($this->_getValidationGroup($formTag));
			}
			$this->_forms[] = $form->setData($this->_getFormData($formTag));
		}
		$this->_postPrepare();
		return $this;
	}

	/**
	 * Hook to do any further setup after preparing the base form.
	 * @return \Application\Service\Update
	 */
	protected function _postPrepare()
	{
		return $this;
	}

	protected function _getForms()
	{
		return array(static::BASE_FORM_NAME => $this->_toForm($this->_getEntity()));
	}

	protected function _getFormData($formTag)
	{
		if ($formTag != static::BASE_FORM_NAME) {
			return $this->_getParam($formTag);
		}
		return $this->_getParams();
	}

	/**
	 * Validates the form
	 */
	protected function _validate()
	{
		$this->_success = true;
		/* @var $form Form */
		foreach ($this->_forms as $form) {
			if (!$form->isValid()) {
				$this->_success = false;
				$this->_mergeFormErrorMessages($form->getMessages());
			}
		}
		$this->_postFormValidate();
		if ($this->success()) {
			$this->_message = $this->_getSuccessMessage();
			$this->_save();
		}
		return $this;
	}

	protected function _mergeFormErrorMessages($messages)
	{
		if (!isset($this->_message)) {
			$this->_message = array();
		}
		$this->_message = array_merge($this->_message, $messages);
	}

	protected function _postFormValidate()
	{
		return $this;
	}

	/**
	 * Saves the update
	 */
	protected function _save()
	{
		$this->_entityManager()->flush();
		return $this;
	}

	/**
	 * Execute any cleanup
	 * @return \Application\Service\Update
	 */
	protected function _postValidate()
	{
		return $this;
	}

	protected function _getEntity()
	{
		if (empty($this->_entity)) {
			throw new UnsetEntityException('No entity to update was set.');
		}
		return $this->_entity;
	}

	protected function _getSuccessMessage()
	{
		return 'Saved!';
	}

	protected function _getParams()
	{
		return $this->_params;
	}

	protected function _getParam($index)
	{
		return isset($this->_params[$index]) ? $this->_params[$index] : null;
	}

	protected function _hasValidationGroup($formName)
	{
		$group = $this->_getValidationGroup($formName);
		return !empty($group);
	}

	protected function _getValidationGroup($formName)
	{
		return isset($this->_validationGroup[$formName]) ? $this->_validationGroup[$formName] : null;
	}

	/**
	 *
	 * @return EntityToForm
	 */
	protected function _e2fService()
	{
		return $this->getServiceLocator()->get('e2f');
	}

	/**
	 *
	 * @param \Application\Entity\Base $entity
	 * @return Form
	 */
	protected function _toForm(Base $entity)
	{
		return $this->_e2fService()->convertEntity($entity);
	}

	/**
	 *
	 * @param string $routeName
	 * @param array $routeParams
	 * @return string
	 */
	protected function _url($routeName, $routeParams = array())
	{
		return $this->getServiceLocator()->get('router')->assemble($routeParams, array('name' => $routeName));
	}

}
