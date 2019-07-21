<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_backend_navigation")
 */
class BackendNavigation extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false, unique=true)
     */
    private $name = null;
    /**
     * @var string|null $parent
     * @ORM\Column(name="parent", type="string", nullable=true, options={"default":"0"})
     */
    private $parent = '0';
    /**
     * @var string|null $path
     * @ORM\Column(name="path", type="string", nullable=true, options={"default":null})
     */
    private $path = null;
    /**
     * @var string|null $icon
     * @ORM\Column(name="icon", type="string", nullable=true, options={"default":null})
     */
    private $icon = null;
    /**
     * @var int $order
     * @ORM\Column(name="sort_order", type="integer", nullable=false, options={"default":1})
     */
    private $order = 1;
    /**
     * @var bool $visible
     * @ORM\Column(name="visible", type="boolean", options={"default":true})
     */
    private $visible = true;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", options={"default":false})
     */
    private $active = false;
    /**
     * @var string $position
     * @ORM\Column(name="position", type="string", nullable=false, options={"default":"sidebar"})
     */
    private $position = 'sidebar';

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
     * @return BackendNavigation
     */
    public function setName(string $name) : BackendNavigation {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getParent() : ?string {
        return $this->parent;
    }

    /**
     * @param string|null $parent
     *
     * @return BackendNavigation
     */
    public function setParent(?string $parent) : BackendNavigation {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath() : ?string {
        return $this->path;
    }

    /**
     * @param string|null $path
     *
     * @return BackendNavigation
     */
    public function setPath(?string $path) : BackendNavigation {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon() : ?string {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     *
     * @return BackendNavigation
     */
    public function setIcon(?string $icon) : BackendNavigation {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrder() : int {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return BackendNavigation
     */
    public function setOrder(int $order) : BackendNavigation {
        $this->order = $order;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVisible() : bool {
        return $this->visible;
    }

    /**
     * @param bool $visible
     *
     * @return BackendNavigation
     */
    public function setVisible(bool $visible) : BackendNavigation {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive() : bool {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return BackendNavigation
     */
    public function setActive(bool $active) : BackendNavigation {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition() : string {
        return $this->position;
    }

    /**
     * @param string $position
     *
     * @return BackendNavigation
     */
    public function setPosition(string $position) : BackendNavigation {
        $this->position = $position;

        return $this;
    }

}
