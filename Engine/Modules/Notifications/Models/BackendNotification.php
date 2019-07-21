<?php

namespace Oforge\Engine\Modules\Notifications\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_backend_notifications")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\Column(name="notification_type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="notification_message", type="string", nullable=false)
     */
    private $message;

    /**
     * @var string
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @var int
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     * @ManyToOne(targetEntity="\Oforge\Engine\Modules\Auth\Models\BackendUser", fetch="EXTRA_LAZY")
     */
    private $userId = null;

    /**
     * @var bool
     * @ORM\Column(name="seen", type="boolean")
     */
    private $seen = false;

    /**
     * @var \DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=true);
     */
    private $timestamp;

    /**
     * @var int
     * @ORM\Column(name="role", type="integer", nullable=true)
     */
    private $role = null;

    /**
     * Triggered on insert
     *
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $this->timestamp = new \DateTime("now");
    }

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
     * @return string | null
     */
    public function getLink() : ?string {
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
     * @return \DateTime|null
     */
    public function getTimestamp() : ?\DateTime {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function getRole() : int {
        return $this->role;
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
     * @param int $role
     *
     * @return BackendNotification
     */
    public function setRole(int $role) : BackendNotification {
        $this->role = $role;

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
