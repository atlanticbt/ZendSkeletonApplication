<?php

namespace Application\Entity\Base;

use Application\Entity\Base;
use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\MappedSuperclass
 *
 * For entities which need auditing like created and last updated
 *
 */
class Tracked extends Base implements TrackedInterface
{

	/**
	 *
	 * @ORM\Column(type="datetime", name="created_ts");
	 * @Annotation\Exclude()
	 *
	 * @var \DateTime
	 */
	protected $createdTs;

	/**
	 *
	 * @ORM\Column(type="datetime", name="last_modified_ts");
	 * @Annotation\Exclude()
	 *
	 * @var \DateTime
	 */
	protected $lastModifiedTs;

	public function __construct()
	{
		parent::__construct();
		$this->setCreatedTs('now')
				->setLastModifiedTs('now');
	}

	public function getCreatedTs()
	{
		return $this->createdTs;
	}

	public function getLastModifiedTs()
	{
		return $this->lastModifiedTs;
	}

	public function setCreatedTs($created)
	{
		$this->createdTs = $this->_acceptStringOrDateTime($created);
		return $this;
	}

	public function setLastModifiedTs($lastModified)
	{
		$this->lastModifiedTs = $this->_acceptStringOrDateTime($lastModified);
		return $this;
	}

	protected function _acceptStringOrDateTime($input)
	{
		if (is_string($input)) {
			return new \DateTime($input);
		}
        if (!$input instanceof \DateTime) {
            throw new \InvalidArgumentException('The value provided is not a valid timestamp.');
        }
        return $input;
	}

}

