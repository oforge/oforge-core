<?php

namespace Oforge\Engine\Modules\Core\Models\Config;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_core_config")
 */
class Config extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;
    /**
     * @var string $group
     * @ORM\Column(name="group_name", type="string", nullable=false)
     */
    private $group;
    /**
     * @var bool $required
     * @ORM\Column(name="required", type="boolean", options={"default":false})
     */
    private $required = false;
    /**
     * @var string $label
     * @ORM\Column(name="label", type="string", nullable=false)
     */
    private $label;
    /**
     * @var string $description
     * @ORM\Column(name="description", type="string", nullable=true, options={"default":null})
     */
    private $description = null;
    /**
     * @var int $order
     * @ORM\Column(name="orderby", type="integer", nullable=false, options={"default":Statics::DEFAULT_ORDER})
     */
    private $order = Statics::DEFAULT_ORDER;
    /**
     * @var string $type
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;
    /**
     * @var array $options
     * @ORM\Column(name="options", type="array")
     */
    private $options;
    /**
     * @var mixed $default
     * @ORM\Column(name="default_value", type="object", nullable=true, options={"default":null})
     */
    private $default = null;
    /**
     * @var Value[]
     * @ORM\OneToMany(targetEntity="Value", mappedBy="config", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="config_id")
     */
    private $values;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Config
     */
    public function setName(string $name) {
        $this->name = strtolower($name);
        if (empty($this->label)) {
            $this->setLabel($this->name);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getGroup() : string {
        return $this->group;
    }

    /**
     * @param string $group
     *
     * @return Config
     */
    public function setGroup($group) : Config {
        $this->group = $group;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired() : bool {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return Config
     */
    public function setRequired(bool $required) {
        $this->required = $required;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() : string {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Config
     */
    public function setLabel(string $label) {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription() : ?string {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Config
     */
    public function setDescription(string $description) {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param string $order
     *
     * @return Config
     */
    public function setOrder($order) : Config {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Config
     */
    public function setType(string $type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getOptions() : ?array {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return Config
     */
    public function setOptions(array $options) {
        $this->options = $options;

        return $this;
    }

    /**
     * @return Value[]
     */
    public function getValues() {
        return $this->values;
    }

    /**
     * @param Value[] $values
     *
     * @return Config
     */
    public function setValues($values) : Config {
        $this->values = $values;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault() {
        return $this->default;
    }

    /**
     * @param mixed $default
     */
    public function setDefault($default) : void {
        $this->default = $default;
    }

}
