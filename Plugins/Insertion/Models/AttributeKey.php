<?php

namespace Insertion\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion_attribute_key")
 * @ORM\Entity
 */
class AttributeKey extends AbstractModel {

    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="attribute_key_name", type="string", nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="attribute_key_type", type="string", nullable=false)
     */
    private $type;

    //TODO: Handle restrictions in a more general way
    /**
     * Restrictions right now are only being used to define how many options a user can select in a multi select element.
     * Other relevant files are attribute-edit.js and Views/Plugins/Insertion/Backend/BackendAttribute/Edit.twig
     */

    /**
     * @var string
     * @ORM\Column(name="attribute_key_restrictions", type="string", nullable=true)
     */
    private $restrictions;

    /**
     * @var string
     * @ORM\Column(name="attribute_key_filter_type", type="string", nullable=false)
     */
    private $filterType;

    /**
     * @ORM\OneToMany(targetEntity="InsertionTypeAttribute", mappedBy="attributeKey")
     * @ORM\JoinColumn(name="attribute_key", referencedColumnName="id")
     */
    private $insertionTypes;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="AttributeValue", mappedBy="attributeKey", cascade={"remove"})
     */
    private $values;

    /**
     * @var boolean
     * @ORM\Column(name="sortable", type="boolean", nullable=false)
     */
    private $sortable = false;

    /**
     * @var boolean
     * @ORM\Column(name="hierarchical", type="boolean", nullable=false)
     */
    private $hierarchical = false;

    public function __construct() {
        $this->values = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getInsertionTypes() {
        return $this->insertionTypes;
    }

    /**
     * @param mixed $insertionTypes
     *
     * @return AttributeKey
     */
    public function setInsertionTypes($insertionTypes) {
        $this->insertionTypes = $insertionTypes;

        return $this;
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
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type) : void {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getRestrictions() : ?string {
        return $this->restrictions;
    }

    /**
     * @param string|null $restrictions
     */
    public function setRestrictions(?string $restrictions) : void {
        $this->restrictions = $restrictions;
    }

    /**
     * @return string
     */
    public function getFilterType() : string {
        return $this->filterType;
    }

    /**
     * @param string $filterType
     *
     * @return AttributeKey
     */
    public function setFilterType(string $filterType) : AttributeKey {
        $this->filterType = $filterType;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @param ArrayCollection $values
     *
     * @return AttributeKey
     */
    public function setValues($values) {
        $this->values = $values;
        return $this;
    }

    /**
     * @param AttributeValue $value
     * @return AttributeKey
     */
    public function addValue($value) {
        if (!$this->values->contains($value)) {
            $this->values->add($value);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable() : bool {
        return $this->sortable;
    }

    /**
     * @param bool $sortable
     * @return AttributeKey
     */
    public function setSortable(bool $sortable) : AttributeKey {
        $this->sortable = $sortable;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHierarchical() : bool {
        return $this->hierarchical;
    }

    /**
     * @param bool $hierarchical
     * @return AttributeKey
     */
    public function setHierarchical(bool $hierarchical) : AttributeKey {
        $this->hierarchical = $hierarchical;

        return $this;
    }

}
