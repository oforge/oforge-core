<?php

namespace Oforge\Engine\Modules\I18n\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_i18n_language")
 * @ORM\Entity
 */
class Language extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="iso", type="string", nullable=false, unique=true)
     */
    private $iso;
    /**
     * @var string
     * @ORM\Column(name="language", type="string", nullable=false)
     */
    private $name;
    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;

    /**
     * @var bool
     * @ORM\Column(name="is_default", type="boolean", nullable=false, options={"default":false})
     */
    private $default = false;

    /**
     * @return int
     */
    public function getId() : ?int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIso() : ?string {
        return $this->iso;
    }

    /**
     * @param string $iso
     */
    public function setIso(string $iso) {
        $this->iso = strtolower($iso);
    }

    /**
     * @return string
     */
    public function getName() : ?string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isActive() : bool {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active) {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isDefault() : bool {
        return $this->default;
    }

    /**
     * @param bool $default
     */
    public function setDefault(bool $default) : void {
        $this->default = $default;
    }

}
