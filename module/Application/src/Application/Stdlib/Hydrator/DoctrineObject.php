<?php

namespace Application\Stdlib\Hydrator;

use Zend\Code\Annotation\AnnotationManager;
use Zend\Code\Reflection\ClassReflection;

/**
 * @author John Foushee <john.foushee@atlanticbt.com>
 *
 * @TODO: need to map the property name to the form field name. In most cases
 * this is the same value (say $firstName => $formData['firstName']) however
 * if the form is annotated so the $firstName property has a form annotation
 * specified name of 'userFirst', the value no longer gets mapped.
 *
 */
class DoctrineObject extends \DoctrineModule\Stdlib\Hydrator\DoctrineObject
{

	/**
	 * Extract values from an object using a by-value logic (this means that it uses the entity
	 * API, in this case, getters)
	 *
	 * @param  object $object
	 * @throws RuntimeException
	 * @return array
	 */
	protected function extractByValue($object)
	{
		/**
		 * Get all properties (even unannotated)
		 */
		$properties = $this->metadata->getReflectionClass()->getProperties();

		$methods = get_class_methods($object);

		$data = array();
		foreach ($properties as $property) {
			$fieldName = $property->getName();
			$getter = 'get' . ucfirst($fieldName);

			// Ignore unknown fields
			if (!in_array($getter, $methods)) {
				continue;
			}
			$fieldValue = $object->$getter();
			if ($fieldValue instanceof \Application\Entity\Base && $fieldValue->isNull()) {
				$fieldValue = null;
			}
			$data[$fieldName] = $this->extractValue($fieldName, $fieldValue);
		}

		return $data;
	}

}
