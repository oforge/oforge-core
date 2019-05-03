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
     * TODO: Relation mapping
     *
     * @var int
     * @ORM\Column(name="insertion_type_id", type="integer", nullable=false)
     * @ORM\ManyToOne(targetEntity="InsertionsType")
     */
    private $insertion_type_id;

    /**
     * @var string
     * @ORM\Column(name="insertion_title", type="string", nullable=false)
     */
    private $title;

    /**
     * TODO: Relation mapping
     * @var int
     * @ORM\Column(name="insertion_user", type="integer", nullable=false)
     */
    private $user;

    /**
     * TODO: Mapping to content type rich text
     *
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
     * @return int
     */
    public function getInsertionTypeId() : int {
        return $this->insertion_type_id;
    }

    /**
     * @param int $insertion_type_id
     */
    public function setInsertionTypeId(int $insertion_type_id) : void {
        $this->insertion_type_id = $insertion_type_id;
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) : void {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user) : void {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getDescription() : string {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description) : void {
        $this->description = $description;
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