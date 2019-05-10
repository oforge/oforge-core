<?php

namespace Insertion\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_type")
 * @ORM\Entity
 */
class InsertionType extends AbstractModel {
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
     * @ORM\OneToOne(targetEntity="InsertionType")
     * @ORM\JoinColumn(name="insertion_parent_id", referencedColumnName="id", nullable=true)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="InsertionTypeAttribute", mappedBy="$insertionTypeId")
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
     *
     * @return InsertionType
     */
    public function setName(string $name) : InsertionType {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getParent() : int {
        return $this->parent;
    }

    /**
     * @param int $parent
     *
     * @return InsertionType
     */
    public function setParent(int $parent) : InsertionType {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param mixed $attributes
     *
     * @return InsertionType
     */
    public function setAttributes($attributes) : InsertionType {
        $this->attributes = $attributes;
        return $this;
    }
}
