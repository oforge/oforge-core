<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_insertion")
 * @ORM\Entity
 */
class Insertion extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="InsertionType", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_type_id", referencedColumnName="id")
     */
    private $insertionType;

    /**
     * @var string
     * @ORM\Column(name="insertion_title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var int
     * @ORM\Column(name="insertion_user", type="integer", nullable=false)
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(name="attribute_key_name", type="text", nullable=false)
     */
    private $description;

    /**
     * @var Datetime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var Datetime
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $this->createdAt = new \DateTime("now");
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->updatedAt = new \DateTime("now");
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getInsertionType() {
        return $this->insertionType;
    }

    /**
     * @param mixed $insertionType
     *
     * @return Insertion
     */
    public function setInsertionType($insertionType) {
        $this->insertionType = $insertionType;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Insertion
     */
    public function setTitle(string $title) : Insertion {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return Insertion
     */
    public function setUser($user) : Insertion {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription() : string {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Insertion
     */
    public function setDescription(string $description) : Insertion {
        $this->description = $description;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt() : DateTime {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt() : DateTime {
        return $this->updatedAt;
    }
}
