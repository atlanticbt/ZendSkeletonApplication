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

	/**
	 * Calls flatten on each of the entities in a collection. If $indexById is true
	 * the returned array is a key/value associative array with the entity ids as the key.
	 * @param array $collection
	 * @param boolean $extended
	 * @param boolean $indexById
	 * @return array
	 */
	protected function _flatCollection($collection, $extended = false, $indexById = false)
	{
		$results = array();
		foreach ($collection as $entity) {
			if (!$entity instanceof BaseInterface) {
				$results[] = $entity;
				continue;
			}
			$value = $entity->flatten($extended);
			if ($indexById) {
				$results[$entity->getId()] = $value;
			} else {
				$results[] = $value;
			}
		}
		return $results;
	}

	/**
	 * Helper method to allow entities to set $this->property = $this->_nullSetter($entity)
	 * which will translate setting a null entity to setting a null value
	 * @param \Application\Entity\BaseInterface $entity
	 * @return null|\Application\Entity\BaseInterface
	 */
	protected function _nullSetter(BaseInterface $entity)
	{
		if ($entity->isNull()) {
			return null;
		}
		return $entity;
	}

	protected function _nullAdd(\Doctrine\Common\Collections\Collection $collection, BaseInterface $entity)
	{
		if (!$entity->isNull()) {
			$collection->add($entity);
		}
	}

	/**
	 * Helper method to allow entities to return
	 * $this->_nullGetter('Propery\Class\Name', $this->property)
	 * and have the entity never return an actual null
	 * @param type $className
	 * @param \Application\Entity\BaseInterface $entity
	 * @return \Application\Entity\nullClass|\Application\Entity\BaseInterface
	 */
	protected function _nullGetter($className, BaseInterface $entity = null)
	{
		$nullClass = $className . 'Null';
		if ($entity === null && class_exists($nullClass)) {
			return new $nullClass();
		}
		return $entity;
	}

}

