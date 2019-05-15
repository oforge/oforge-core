<?php

namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_attribute_value")
 * @ORM\Entity
 */
class AttributeValue extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="attribute_value", type="string", nullable=false)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Insertion\Models\AttributeKey", inversedBy="values")
     * @ORM\JoinColumn(name="attribute_key", referencedColumnName="id")
     */
    private $attributeKey;

    /**
     * @ORM\ManyToOne(targetEntity="AttributeKey")
     * @ORM\JoinColumn(name="attribute_value_sub_attribute_key", referencedColumnName="id", nullable=true)
     */
    private $subAttributeKey;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getValue() : string {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return AttributeValue
     */
    public function setValue(string $value) : AttributeValue {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributeKey() {
        return $this->attributeKey;
    }

    /**
     * @param mixed $attributeKey
     *
     * @return AttributeValue
     */
    public function setAttributeKey($attributeKey) {
        $this->attributeKey = $attributeKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubAttributeKey() {
        return $this->subAttributeKey;
    }

    /**
     * @param mixed $subAttributeKey
     *
     * @return AttributeValue
     */
    public function setSubAttributeKey($subAttributeKey) {
        $this->subAttributeKey = $subAttributeKey;

        return $this;
    }
}
