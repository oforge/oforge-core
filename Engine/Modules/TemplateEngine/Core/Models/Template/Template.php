<?php

namespace Oforge\Engine\Modules\TemplateEngine\Core\Models\Template;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_template_engine_template")
 * @ORM\Entity
 */
class Template extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="template_name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean")
     */
    private $active = false;

    /**
     * @var bool
     * @ORM\Column(name="installed", type="boolean")
     */
    private $installed = false;

    /**
     * @var int
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId = null;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Template
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param bool $active
     *
     * @return Template
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @param bool $installed
     *
     * @return Template
     */
    public function setInstalled($installed) {
        $this->installed = $installed;

        return $this;
    }

    /**
     * @return bool
     */
    public function getInstalled() {
        return $this->installed;
    }

    /**
     * @return int|null
     */
    public function getParentId() : ?int {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId($parentId) {
        $this->parentId = $parentId;
    }
}
