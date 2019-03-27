<?php

namespace FrontendUserManagement\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Helper\Helper;

/**
 * @ORM\Table(name="frontend_user_management_user")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User extends AbstractModel
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
     * @ORM\Column(name="email", type="string", nullable=false, unique=true)
     */
    private $email;
    
    /**
     * @var string
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    private $password;

    /**
     * @var string
     * @ORM\Column(name="guid", type="guid", nullable=false)
     */
    private $guid;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */

    private $createdAt;
    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */

    private $updatedAt;

    /**
     * @var bool $active
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;

    public function __construct() {
        $dateTimeNow = new \DateTime('now');
        $this->createdAt = $dateTimeNow;
        $this->updatedAt = $dateTimeNow;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new \DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function updatedGuid(): void {
        $newGuid = Helper::generateGuid();
        $this->setGuid($newGuid);
    }
    
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
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     */
    public function setEmail(string $email)
    {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * @param string $password
     */
    public function setPassword(string $password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getGuid() {
        return $this->guid;
    }

    /**
     * @param mixed $guid
     */
    public function setGuid($guid) : void {
        $this->guid = $guid;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt() : ?\DateTime {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt) : void {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt() : ?\DateTime {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt) : void {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool
     */
    public function getActive() : bool {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active) : void {
        $this->active = $active;
    }
}
