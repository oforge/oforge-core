<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Entity
 * @ORM\Table(name="oforge_backend_user_dashboard_widget")
 */
class UserDashboardWidget extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var int $widgetId
     * @ORM\Column(name="widget_id", type="integer", nullable=false)
     */
    private $widgetId = null;
    /**
     * @var int $userId
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;
    /**
     * @var string $position
     * @ORM\Column(name="position", type="string", nullable=false, options={"default":""})
     */
    private $position = '';
    /**
     * @var int $order
     * @ORM\Column(name="sort_order", type="integer", nullable=false, options={"default":0})
     */
    private $order = 0;
    /**
     * @var string $cssClass
     * @ORM\Column(name="css_class", type="string", nullable=false, options={"default":""})
     */
    private $cssClass = '';

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getWidgetId() : int {
        return $this->widgetId;
    }

    /**
     * @param int $widgetId
     *
     * @return UserDashboardWidget
     */
    public function setWidgetId(int $widgetId) : UserDashboardWidget {
        $this->widgetId = $widgetId;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId() : int {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return UserDashboardWidget
     */
    public function setUserId(int $userId) : UserDashboardWidget {
        $this->userId = $userId;

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
     * @return UserDashboardWidget
     */
    public function setActive(bool $active) : UserDashboardWidget {
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
     * @return UserDashboardWidget
     */
    public function setPosition(string $position) : UserDashboardWidget {
        $this->position = $position;

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
     * @return UserDashboardWidget
     */
    public function setOrder(int $order) : UserDashboardWidget {
        $this->order = $order;

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
     * @return UserDashboardWidget
     */
    public function setCssClass(string $cssClass) : UserDashboardWidget {
        $this->cssClass = $cssClass;

        return $this;
    }

}
