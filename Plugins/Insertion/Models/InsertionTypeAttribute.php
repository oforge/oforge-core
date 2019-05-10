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
     * @var int
     * @ORM\Column(name="attribute_key_id", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="AttributeKey")
     * @ORM\JoinColumn(nullable=false)
     */
    private $attributeKeyId;

    /**
     * @var int
     * @ORM\Column(name="insertion_type_id", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="InsertionType")
     * @ORM\JoinColumn(nullable=false)
     */
    private $insertionTypeId;

    /**
     * @var boolean
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required;

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
