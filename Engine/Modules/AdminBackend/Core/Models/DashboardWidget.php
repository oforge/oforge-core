<?php
namespace Oforge\Engine\Modules\AdminBackend\Core\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
/**
 * @ORM\Table(name="oforge_backend_dashboard_widget")
 * @ORM\Entity
 */
class DashboardWidget extends AbstractModel
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
     * @ORM\Column(name="action", type="string", nullable=true)
     */
    private $action = null;

    /**
     * @var string
     * @ORM\Column(name="template_name", type="string", nullable=true)
     */
    private $templateName = null;


    /**
     * @var string
     * @ORM\Column(name="icon", type="string", nullable=true)
     */
    private $icon = null;

    /**
     * @var string
     * @ORM\Column(name="i18n_name", type="string", nullable=false, unique=true)
     */
    private $name = null;

    /**
     * @var string
     * @ORM\Column(name="i18n_title", type="string", nullable=true)
     */
    private $title = null;


    /**
     * @var string
     * @ORM\Column(name="position", type="string", nullable=true)
     */
    private $position = null;

    /**
     * @var string
     * @ORM\Column(name="css_class", type="string", nullable=true)
     */
    private $cssClass = null;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return DashboardWidget
     */
    public function setAction(string $action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return DashboardWidget
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
        return $this;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return DashboardWidget
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return DashboardWidget
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
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
     * @return DashboardWidget
     */
    public function setPosition(string $position) {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getCssClass() : string {
        return $this->cssClass;
    }

    /**
     * @param string $cssClass
     *
     * @return DashboardWidget
     */
    public function setCssClass(string $cssClass) : DashboardWidget {
        $this->cssClass = $cssClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateName() : string {
        return $this->templateName;
    }

    /**
     * @param string $templateName
     *
     * @return DashboardWidget
     */
    public function setTemplateName(string $templateName) : DashboardWidget {
        $this->templateName = $templateName;
        return $this;
    }
}
