<?php

namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_type_attribute")
 * @ORM\Entity
 */
class InsertionTypeAttribute extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AttributeKey", inversedBy="insertionTypes")
     * @ORM\JoinColumn(name="attribute_key", referencedColumnName="id", nullable=false)
     */
    private $attributeKey;

    /**
     * @ORM\ManyToOne(targetEntity="InsertionType", inversedBy="attributes")
     * @ORM\JoinColumn(name="insertion_type", referencedColumnName="id", nullable=false)
     */
    private $insertionType;

    /**
     * @var boolean
     * @ORM\Column(name="is_top", type="boolean")
     */
    private $isTop = false;

    /**
     * @var string
     * @ORM\Column(name="attribute_group", type="string", nullable=true)
     */
    private $attributeGroup;

    /**
     * @var boolean
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required = false;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
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
     * @return InsertionTypeAttribute
     */
    public function setAttributeKey($attributeKey) {
        $this->attributeKey = $attributeKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInsertionType() {
        return $this->insertionType;
    }

    /**
     * @param mixed $insertionType
     *
     * @return InsertionTypeAttribute
     */
    public function setInsertionType($insertionType) {
        $this->insertionType = $insertionType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTop() : bool {
        return $this->isTop;
    }

    /**
     * @param bool $isTop
     *
     * @return InsertionTypeAttribute
     */
    public function setIsTop(bool $isTop) : InsertionTypeAttribute {
        $this->isTop = $isTop;

        return $this;
    }

    /**
     * @return string
     */
    public function getAttributeGroup() : string {
        return $this->attributeGroup;
    }

    /**
     * @param string $attributeGroup
     *
     * @return InsertionTypeAttribute
     */
    public function setAttributeGroup(string $attributeGroup) : InsertionTypeAttribute {
        $this->attributeGroup = $attributeGroup;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired() : bool {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return InsertionTypeAttribute
     */
    public function setRequired(bool $required) : InsertionTypeAttribute {
        $this->required = $required;

        return $this;
    }
}
