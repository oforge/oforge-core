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
     * @ORM\ManyToOne(targetEntity="AttributeKey", inversedBy="values", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="attribute_key_id", referencedColumnName="id")
     */
    private $attributeKeyId;

    /**
     * @ORM\OneToOne(targetEntity="AttributeKey", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="attribute_value_sub_attribute_key_id", referencedColumnName="id", nullable=true)
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
     * @return AttributeValue
     */
    public function setValue(string $value) : AttributeValue {
        $this->value = $value;
        return $this;
    }

    /**
     * @return null|int
     */
    public function getSubAttributeKeyId() : ?AttributeKey {
        return $this->subAttributeKeyId;
    }

    /**
     * @param AttributeKey $subAttributeKeyId
     *
     * @return AttributeValue
     */
    public function setSubAttributeKeyId(AttributeKey $subAttributeKeyId) : AttributeValue {
        $this->subAttributeKeyId = $subAttributeKeyId;
        return $this;
    }
}