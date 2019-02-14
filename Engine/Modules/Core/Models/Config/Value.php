<?php
namespace Oforge\Engine\Modules\Core\Models\Config;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
/**
 * @ORM\Table(name="oforge_core_config_values")
 * @ORM\Entity
 */
class Value extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="Element", inversedBy="values", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="element_id", referencedColumnName="id")
     */
    private $element;

    /**
     * @var string
     * @ORM\Column(name="value", type="object", nullable=true)
     */
    private $value;

    /**
     * @var string
     * @ORM\Column(name="scope", type="integer", nullable=true)
     */
    private $scope;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Set element
     *
     * @param Element $element
     *
     * @return Value
     */
    public function setElement($element)
    {
        $this->element = $element;
        return $this;
    }
    /**
     * Get element
     *
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }
   
    /**
     * Set value
     *
     * @param mixed $value
     *
     * @return Value
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $scope
     *
     * @return Element
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }
    /**
     * @return int
     */
    public function getScope()
    {
        return $this->scope;
    }
}