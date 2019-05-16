<?php

namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_insertion_attribute_value")
 * @ORM\Entity
 */
class InsertionAttributeValue extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AttributeKey", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="attribute_key", referencedColumnName="id", nullable=false)
     */
    private $attributeKey;

    /**
     * @ORM\ManyToOne(targetEntity="Insertion", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_id", referencedColumnName="id", nullable=false)
     */
    private $insertion;

    /**
     * @var string
     * @ORM\Column(name="insertion_attribute_value", type="string", nullable=true)
     */
    private $value;

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
     * @return InsertionAttributeValue
     */
    public function setAttributeKey($attributeKey) {
        $this->attributeKey = $attributeKey;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInsertion() {
        return $this->insertion;
    }

    /**
     * @param mixed $insertion
     *
     * @return InsertionAttributeValue
     */
    public function setInsertion($insertion) {
        $this->insertion = $insertion;

        return $this;
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
     * @return InsertionAttributeValue
     */
    public function setValue(string $value) : InsertionAttributeValue {
        $this->value = $value;
        return $this;
    }
}
