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
     * @var int
     * @ORM\Column(name="attribute_key_id", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="AttributeKey")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attributeKeyId;


    /**
     * @var int
     * @ORM\Column(name="insertion_id", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="Insertion")
     * @ORM\JoinColumn(nullable=false)
     */
    private $insertionId;

    /**
     * @var string
     * @ORM\Column(name="insertion_attribute_value", type="string", nullable=true)
     */
    private $value;

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
