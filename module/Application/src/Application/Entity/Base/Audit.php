<?php
namespace Application\Entity\Base;

use Application\Entity\Base;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit")
 */
class Audit extends Base {

    /**
     * @ORM\ManyToOne(targetEntity="Application\Entity\Base\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var \Application\Entity\Base\User\CompanyAdmin
     */
    protected $user;


    /**
     * @ORM\Column(type="string", length=256)
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="text", length=256)
     *
     * @var string
     */
    protected $delta;


    /**
     * @ORM\Column(type="datetime", name="performed_ts");
     *
     * @var \DateTime
     */
    protected $performed;
    /**
     * @ORM\Column(type="string", name="object_class", length=256)
     *
     * @var string
     */
    protected $objectClass;

    /**
     * @ORM\Column(type="integer", name="object_id", length=256)
     *
     * @var string
     */
    protected $objectId;

    /**
     * @return UserInterface
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user) {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param $type
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDelta() {
        return unserialize($this->delta);
    }

    /**
     * @param $delta
     * @return $this
     */
    public function setDelta($delta) {
        $this->delta = serialize($delta);
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPerformedTs() {
        return $this->performed;
    }

    /**
     * @param \DateTime $performed
     * @return $this
     */
    public function setPerformedTs(\DateTime $performed) {
        $this->performed = $performed;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectClass() {
        return $this->objectClass;
    }

    /**
     * @param $class
     * @return $this
     */
    public function setObjectClass($class) {
        $this->objectClass = $class;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectId() {
        return $this->objectId;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setObjectId($id) {
        $this->objectId = $id;
        return $this;
    }

    public function __construct() {
        $this->performed = new \DateTime();
    }
}