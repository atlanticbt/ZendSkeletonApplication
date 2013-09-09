<?php

namespace Application\Form\Validator;

use Doctrine\ORM\EntityManager;
use Zend\Validator\AbstractValidator;

class DoctrineNoRecordExists extends AbstractValidator
{

	const RECORD_EXISTS = 'exists';

	protected $messageTemplates = array(
		self::RECORD_EXISTS => "An entry already exists with the value '%value%'",
	);

	/**
	 *
	 * @var EntityManager
	 */
	private $_em;

	/**
	 *
	 * @var string
	 */
	private $_entityClass;

	/**
	 *
	 * @var string
	 */
	private $_entityProperty;

	public function __construct($options = null)
	{
		$this->_em = $options['entityManager'];
		$this->_entityClass = $options['class'];
		$this->_entityProperty = $options['property'];
		if (isset($options['errorMessage'])) {
			$this->messageTemplates[static::RECORD_EXISTS] = $options['errorMessage'];
		}
		parent::__construct($options);
	}

	public function isValid($value)
	{
		$result = $this->_em->getRepository($this->_entityClass)->findOneBy(array($this->_entityProperty => $value));
		$valid = true;
		if (!empty($result)) {
			$this->error(static::RECORD_EXISTS, $value);
			$valid = false;
		}
		return $valid;
	}

}

