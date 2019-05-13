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
     * @ORM\Column(name="attribute_key_id", nullable=false)
     * @ORM\ManyToOne(targetEntity="AttributeKey")
     * @ORM\JoinColumn(name="attribute_key_id", referencedColumnName="id", nullable=false)
     */
    private $attributeKeyId;

    /**
     * @ORM\Column(name="insertion_type_id", nullable=false)
     * @ORM\ManyToOne(targetEntity="InsertionType", inversedBy="attributes")
     * @ORM\JoinColumn(name="insertion_type_id", referencedColumnName="id", nullable=false)
     */
    private $insertionTypeId;

    /**
     * @var boolean
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
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

    /**
     * @param int $insertionTypeId
     *
     * @return InsertionTypeAttribute
     */
    public function setInsertionTypeId(int $insertionTypeId) : InsertionTypeAttribute {
        $this->insertionTypeId = $insertionTypeId;

        return $this;
}

    /**
     * @return mixed
     */
    public function getInsertionTypeId() {
        return $this->insertionTypeId;
    }

    /**
     * @return mixed
     */
    public function getAttributeKeyId() {
        return $this->attributeKeyId;
    }

    /**
     * @param int $attributeKeyId
     *
     * @return InsertionTypeAttribute
     */
    public function setAttributeKeyId(int $attributeKeyId) : InsertionTypeAttribute {
        $this->attributeKeyId = $attributeKeyId;

        return $this;
    }

}
