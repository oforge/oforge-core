<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_backend_dashboard_widget")
 */
class DashboardWidget extends AbstractModel {
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
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;
    /**
     * @var string $template
     * @ORM\Column(name="template", type="string", nullable=false)
     */
    private $template;
    /**
     * @var string|null $handler
     * @ORM\Column(name="handler", type="string", nullable=true, options={"default":null})
     */
    private $handler = null;
    /**
     * @var string|null $label
     * @ORM\Column(name="label", type="string", nullable=true, options={"default":null})
     */
    private $label = null;
    /**
     * @var string|null $position
     * @ORM\Column(name="position", type="string", nullable=true, options={"default":null})
     */
    private $position = null;
    /**
     * @var string|null $cssClass
     * @ORM\Column(name="css_class", type="string", nullable=true, options={"default":null})
     */
    private $cssClass = null;

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
     * @return DashboardWidget
     */
    public function setName(string $name) : DashboardWidget {
        $this->name = $name;

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
     * @return DashboardWidget
     */
    public function setActive(bool $active) : DashboardWidget {
        $this->active = $active;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate() : string {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return DashboardWidget
     */
    public function setTemplate(string $template) : DashboardWidget {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHandler() : ?string {
        return $this->handler;
    }

    /**
     * @param string|null $handler
     *
     * @return DashboardWidget
     */
    public function setHandler(?string $handler) : DashboardWidget {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel() : ?string {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return DashboardWidget
     */
    public function setLabel(?string $label) : DashboardWidget {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPosition() : ?string {
        return $this->position;
    }

    /**
     * @param string|null $position
     *
     * @return DashboardWidget
     */
    public function setPosition(?string $position) : DashboardWidget {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCssClass() : ?string {
        return $this->cssClass;
    }

    /**
     * @param string|null $cssClass
     *
     * @return DashboardWidget
     */
    public function setCssClass(?string $cssClass) : DashboardWidget {
        $this->cssClass = $cssClass;

        return $this;
    }

}
