<?php

namespace Oforge\Engine\Modules\AdminBackend\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_backend_user_favorites")
 * @ORM\Entity
 */
class BackendUserFavorites extends AbstractModel
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
     * @ORM\Column(name="path", type="string", nullable=true)
     */
    private $path = null;

    /**
     * @var string
     * @ORM\Column(name="icon", type="string", nullable=true)
     */
    private $icon = null;

    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name = null;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title = null;

    /**
     * @var integer
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId = true;


    /**
     * @var boolean
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $active = true;

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
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): BackendUserFavorites
    {
        $this->path = $path;
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
     */
    public function setIcon(string $icon): BackendUserFavorites
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): BackendUserFavorites
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return integer
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): BackendUserFavorites
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): BackendUserFavorites
    {
        $this->active = $active;
        return $this;
    }
}