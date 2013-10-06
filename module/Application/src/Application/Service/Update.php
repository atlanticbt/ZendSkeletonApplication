<?php

namespace Application\Service;

use Application\Service\Permission;
use Application\Entity\BaseInterface;
use Application\Entity\Base;
use Application\Service\EntityToForm;
use Zend\Form\Form;
use Application\Service\Update\Exception\UnsetEntity as UnsetEntityException;
use Application\Service\Lookup\Exception\FailedLookup as FailedLookupException;

class Update extends BaseService implements UpdateInterface
{
	const BASE_FORM_NAME = 'entity';

	/**
	 *
	 * @var Base
	 */
	protected $_entity;

	/**
	 * Array containing any other entities which might interact with this one.
	 * @var array
	 */
	protected $_sideEntities;

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
	 * Associative array of callables which will be called after an invokation
	 * of _generateSideEntities(). This will perform any extra attachments which
	 * may be necessary.
	 * Callable should expect 2 parameters:
	 * 	(string) $key
	 * 	(\Application\Entity\BaseInterface) $entity
	 * Nothing is expected in return.
	 *
	 * @var array
	 */
	protected $_attachEntityCallables = array();

	/**
	 * Associative array of callables which will be called when a form with a
	 * matching key is being created from an entity (during _getForms() process)
	 * Callable should expect 3 parameters:
	 * 	(string) $key
	 * 	(\Zend\Form\Form) $form
	 * 	(\Application\Entity\BaseInterface) $entity
	 *
	 * And should return \Zend\Form\Form object
	 *
	 * @var array
	 */
	protected $_formConfigCallables = array();

	/**
	 * Associative array of callables which will be called when a form with a
	 * matching key is being validated (during _validate() process). Callable
	 * should expect 2 parameters:
	 * 	(string) $key
	 * 	(\Zend\Form\Form) $form
	 * And should return a boolean indicating whether the form validated.
	 * @var array
	 */
	protected $_formValidateCallables = array();

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
	 * @param \Application\Entity\BaseInterface $entity
	 * @return \Application\Service\Update
	 */
	public function setEntity(BaseInterface $entity)
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
	 * Returns the base entity if $type is not set, else returns side entity
	 * @param string|null $type
	 * @return Base
	 */
	public function entity($type = null)
	{
		$sideEntities = $this->_getSideEntities();
		if (isset($sideEntities[$type])) {
			return $sideEntities[$type];
		}
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
		$this->_prePrepare();
		foreach ($this->_getForms() as $formTag => $form) {
			if ($this->_hasValidationGroup($formTag)) {
				$form->setValidationGroup($this->_getValidationGroup($formTag));
			}
			$form->setData($this->_getFormData($formTag));
		}
		$this->_postPrepare();
		return $this;
	}

	/**
	 * Hook to set up any data before the prepare function runs and sets validation groups/data
	 * @return \Application\Service\Update
	 */
	protected function _prePrepare()
	{
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

	/**
	 * Returns an associative array of entities which will go with the main entity
	 * @return array
	 */
	protected function _generateSideEntities()
	{
		return array();
	}

	protected function _attachSideEntities(array $entities)
	{
		foreach ($entities as $key => $entity) {
			$this->_callForForm($this->_attachEntityCallables, $key, function($key, BaseInterface $entity) {

					}, array($key, $entity));
		}
		return $entities;
	}

	/**
	 * Gets side entities and stores a local reference. Override _generateSideEntities
	 * to add entities to this array.
	 * @return array
	 */
	protected function _getSideEntities()
	{
		if (!isset($this->_sideEntities)) {
			$this->_sideEntities = $this->_attachSideEntities($this->_generateSideEntities());
		}
		return $this->_sideEntities;
	}

	protected function _getFormConfigCallables()
	{
		return $this->_formConfigCallables;
	}

	/**
	 * Returns an associative array of 'key' => \Zend\Form\Form pairs.
	 * @return type
	 */
	protected function _getForms()
	{
		if (empty($this->_forms)) {
			$this->_forms = array();
			foreach (array_merge($this->_getSideEntities(), array(static::BASE_FORM_NAME => $this->_getEntity())) as $key => $entity) {
				$form = $this->_callForForm($this->_getFormConfigCallables(), $key, function($key, $form, $entity) {
							return $form;
						}, array($key, $this->_toForm($entity), $entity));
				$this->_forms[$key] = $form;
			}
		}
		return $this->_forms;
	}

	/**
	 * Gets param data for a form. The main entity gets all parameters, all side
	 * entity forms will get the data in the param at index $formTag.
	 * Example: when updating a
	 * @param string $formTag
	 * @return mixed
	 */
	protected function _getFormData($formTag)
	{
		if ($formTag != static::BASE_FORM_NAME) {
			return $this->_filterFormData($this->_getParam($formTag));
		}
		return $this->_filterFormData($this->_getParams());
	}

	protected function _filterFormData($data)
	{
		return is_array($data) ? $data : array();
	}

	/**
	 * Allows you to set/override an individual parameter value
	 * @param string $key
	 * @param mixed $value
	 * @param string|null $formTag
	 * @return \Application\Service\Update
	 */
	protected function _setFormData($key, $value, $formTag = null)
	{
		if (empty($formTag) || $formTag == static::BASE_FORM_NAME) {
			$this->_params[$key] = $value;
		} else {
			$this->_params[$formTag][$key] = $value;
		}
		return $this;
	}

	/**
	 * Allows subclasses to skip validating side entity forms as desired.
	 * @param string $key
	 * @param \Zend\Form\Form $form
	 * @return boolean
	 */
	protected function _validateForm($key, Form $form)
	{
		return $form->isValid();
	}

	/**
	 * Validates the form
	 * @return Update
	 */
	protected function _validate()
	{
		$this->_success = true;
		/* @var $form Form */
		foreach ($this->_getForms() as $key => $form) {
			if (!$this->_callForForm($this->_formValidateCallables, $key, array($this, '_validateForm'), array($key, $form))) {
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

	/**
	 * When validating multiple forms, this merges any error messages into a single
	 * array to be returned.
	 * @param type $messages
	 */
	protected function _mergeFormErrorMessages($messages)
	{
		if (!isset($this->_message)) {
			$this->_message = array();
		}
		if (!is_array($messages)) {
			$messages = array($messages);
		}
		$this->_message = array_merge($this->_message, $messages);
	}

	/**
	 * Hook to allow for any action after the form validation.
	 * @return \Application\Service\Update
	 */
	protected function _postFormValidate()
	{
		return $this;
	}

	/**
	 * Saves the update
	 * @return Update
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

	/**
	 * Returns the main entity to update.
	 * @return Base
	 * @throws UnsetEntityException
	 */
	protected function _getEntity()
	{
		if (empty($this->_entity)) {
			throw new UnsetEntityException('No entity to update was set.');
		}
		return $this->_entity;
	}

	/**
	 *
	 * @return string
	 */
	protected function _getSuccessMessage()
	{
		return 'Saved!';
	}

	/**
	 *
	 * @return array
	 */
	protected function _getParams()
	{
		return $this->_params;
	}

	/**
	 *
	 * @param string $index
	 * @return mixed
	 */
	protected function _getParam($index)
	{
		return isset($this->_params[$index]) ? $this->_params[$index] : null;
	}

	/**
	 *
	 * @param string $formName
	 * @return boolean
	 */
	protected function _hasValidationGroup($formName)
	{
		$group = $this->_getValidationGroup($formName);
		return !empty($group);
	}

	/**
	 *
	 * @param string $formName
	 * @return array|null
	 */
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

	/**
	 *
	 * @param array $array
	 * @param string $key
	 * @param callable $default
	 * @return mixed
	 * @throws \InvalidArgumentException
	 */
	protected function _callForForm(array $array, $key, $default, array $params = array())
	{
		$callable = isset($array[$key]) ? $array[$key] : $default;
		if (!is_callable($callable)) {
			throw new \InvalidArgumentException('No valid callable was found.');
		}
		return call_user_func_array($callable, $params);
	}

	/**
	 * Invokes a lookup service for the type specified in string.
	 * @param string $type
	 * @return BaseInterface
	 * @throws FailedLookupException
	 */
	protected function _lookup($type)
	{
		/* @var $factory \Application\Factory\Lookup */
		$factory = $this->getServiceLocator()->get('lookup_factory');

		return $factory->configure($type, $this->_getParams())->getService()->lookup();
	}

}
