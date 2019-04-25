<?php

namespace Insertion\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_type")
 * @ORM\Entity
 */
class Insertion_Type extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="insertion_type_name", type="string", nullable=false)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="Insertion_Type")
     * @ORM\JoinColumn(name="insertion_parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @ORM\ManyToMany(targetEntity="Attribute_Key")
     * @ORM\JoinTable(name="oforge_insertion_type_attribute",
     *                joinColumns={@ORM\JoinColumn(name="insertion_type_id", referencedColumnName="id")},
     *                inverseJoinColumns={@ORM\JoinColumn(name="attribute_key_id", referencedColumnName="id")}
     * )
     */
    private $attributes;

    public function __construct() {
        $this->attributes = new ArrayCollection();
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
     * @return int
     */
    public function getParent() : int {
        return $this->parent;
    }

    /**
     * @param int $parent
     */
    public function setParent(int $parent) : void {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     */
    public function setAttributes($attributes) : void {
        $this->attributes = $attributes;
    }



}