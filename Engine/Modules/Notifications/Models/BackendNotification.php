<?php

namespace Oforge\Engine\Modules\Notifications\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_backend_notifications")
 * @ORM\Entity
 */
class BackendNotification extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", nullable=false)
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @var int
     * @ORM\Column(name="userId", type="integer", nullable=true)
     * @ManyToOne(targetEntity="\Oforge\Engine\Modules\Auth\Models\BackendUser")
     */
    private $userId = null;

    /**
     * @var bool
     * @ORM\Column(name="seen", type="boolean")
     */
    private $seen = false;

    /**
     * @var string
     * @ORM\Column(name="timestamp", type="datetime", nullable=false);
     */
    private $timestamp;

    /**
     * @var int
     * @ORM\Column(name="group", type="integer", nullable=true)
     */
    private $group = null;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getLink() : string {
        return $this->link;
    }

    /**
     * @return int
     */
    public function getUserId() : int {
        return $this->userId;
    }

    /**
     * @return bool
     */
    public function isSeen() : bool {
        return $this->seen;
    }

    /**
     * @return string
     */
    public function getTimestamp() : string {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getGroup() : int {
        return $this->group;
    }

    /**
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

    /**
     * @param string $type
     *
     * @return BackendNotification
     */
    public function setType(string $type) : BackendNotification {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $link
     *
     * @return BackendNotification
     */
    public function setLink(string $link) : BackendNotification {
        $this->link = $link;

        return $this;
    }

    /**
     * @param int $userId
     *
     * @return BackendNotification
     */
    public function setUserId(int $userId) : BackendNotification {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param bool $seen
     *
     * @return BackendNotification
     */
    public function setSeen(bool $seen) : BackendNotification {
        $this->seen = $seen;

        return $this;
    }

    /**
     * @param int $group
     *
     * @return BackendNotification
     */
    public function setGroup(int $group) : BackendNotification {
        $this->group = $group;

        return $this;
    }

    /**
     * @param string $message
     *
     * @return BackendNotification
     */
    public function setMessage(string $message) : BackendNotification {
        $this->message = $message;

        return $this;
    }
}
