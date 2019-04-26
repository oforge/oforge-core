<?php

namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_attribute_value")
 * @ORM\Entity
 */
class Insertion_Attribute_Value extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * TODO: Relation mapping
     * @var int
     * @ORM\Column(name="attribute_key_id", type="integer", nullable=false)
     */
    private $attributeKeyId;


    /**
     * TODO: Relation mapping
     * @var int
     * @ORM\Column(name="insertion_id", type="integer", nullable=false)
     */
    private $insertionTypeKeyId;

    /**
     * TODO: Check if nullable is okay
     * @var string
     * @ORM\Column(name="insertion_attribute_value", type="string", nullable=true)
     */
    private $value;


}