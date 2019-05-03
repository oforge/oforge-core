<?php

namespace Insertion\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_attribute_key")
 * @ORM\Entity
 */
class AttributeKey extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="attribute_key_name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="attribute_key_type", type="string", nullable=false)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="Attribute_Value")
     * @ORM\JoinTable(name="oforge_attribute_key_value",
     *                joinColumns={@ORM\JoinColumn(name="attribute_key_id", referencedColumnName="id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="attribute_value_id", referencedColumnName="id", unique=true)}
     * )
     */
    private $values;

    public function __construct() {
        $this->values = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) : void {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @param mixed $values
     *
     * @return Attribute_Key
     */
    public function setValues($values) {
        $this->values = $values;

        return $this;
    }

}
