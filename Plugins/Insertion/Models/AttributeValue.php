<?php

namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_attribute_value")
 * @ORM\Entity
 */
class Attribute_Value extends AbstractModel {
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
     * TODO: Relation mapping
     * @var int
     * @ORM\Column(name="attribute_value_sub_attribute_key_id", type="integer", nullable=true)
     */
    private $subAttributeKeyId;

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
     * @return Attribute_Value
     */
    public function setValue(string $value) : Attribute_Value {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getSubAttributeKeyId() : int {
        return $this->subAttributeKeyId;
    }

    /**
     * @param int $subAttributeKeyId
     *
     * @return Attribute_Value
     */
    public function setSubAttributeKeyId(int $subAttributeKeyId) : Attribute_Value {
        $this->subAttributeKeyId = $subAttributeKeyId;

        return $this;
    }
}