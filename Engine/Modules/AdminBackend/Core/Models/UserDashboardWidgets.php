<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_backend_user_dashboard_widgets")
 * @ORM\Entity
 */
class UserDashboardWidgets extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userId = null;

    /**
     * @var int
     * @ORM\Column(name="widget_id", type="integer", nullable=false)
     */
    private $widgetId = null;

    /**
     * @var string
     * @ORM\Column(name="position", type="string", nullable=false)
     */
    private $position = "";

    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=false)
     */
    private $order = 1;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
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
     * @return UserDashboardWidgets
     */
    public function setOrder(int $order) : UserDashboardWidgets {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition() : string{
        return $this->position;
    }

    /**
     * @param string $position
     *
     * @return UserDashboardWidgets
     */
    public function setPosition(string $position) : UserDashboardWidgets {
        $this->position = $position;

        return $this;
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
     * @return UserDashboardWidgets
     */
    public function setWidgetId(int $widgetId) : UserDashboardWidgets {
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
     * @return UserDashboardWidgets
     */
    public function setUserId(int $userId) : UserDashboardWidgets {
        $this->userId = $userId;
        return $this;
    }

}
