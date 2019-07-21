<?php

namespace Oforge\Engine\Modules\Auth\Models\User;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\MappedSuperclass()
 * @ORM\HasLifecycleCallbacks
 */
class BaseUser extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $email
     * @ORM\Column(name="email", type="string", nullable=false, unique=true)
     */
    private $email;
    /**
     * @var string $password
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private $password;
    /**
     * @var DateTimeImmutable $createdAt
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false)
     */
    private $createdAt;
    /**
     * @var DateTimeImmutable $updatedAt
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=false)
     */
    private $updatedAt;
    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;

    public function __construct() {
        $dateTimeNow     = new DateTimeImmutable('now');
        $this->createdAt = $dateTimeNow;
        $this->updatedAt = $dateTimeNow;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps() : void {
        $dateTimeNow     = new DateTimeImmutable('now');
        $this->updatedAt = $dateTimeNow;
        if ($this->createdAt === null) {
            $this->createdAt = $dateTimeNow;
        }
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return BaseUser
     */
    public function setId(int $id) : BaseUser {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() : string {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return BaseUser
     */
    public function setEmail(string $email) : BaseUser {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() : string {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return BaseUser
     */
    public function setPassword(string $password) : BaseUser {
        $this->password = $password;

        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt() : DateTimeImmutable {
        return $this->createdAt;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdatedAt() : DateTimeImmutable {
        return $this->updatedAt;
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
     * @return BaseUser
     */
    public function setActive(bool $active) : BaseUser {
        $this->active = $active;

        return $this;
    }

}
