<?php

namespace Oforge\Engine\Modules\Core\Models\Store;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_core_store_key_value")
 */
class KeyValue extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="key_name", type="string", nullable=false, unique=true)
     */
    private $name;
    /**
     * @var string $value
     * @ORM\Column(name="value", type="string", nullable=false)
     */
    private $value;

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
    protected function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue() : string {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value) {
        $this->value = $value;
    }

}
