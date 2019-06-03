<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * @ORM\Table(name="oforge_insertion_user_bookmarks")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionUserBookmark extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="FrontendUserManagement\Models\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_user", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Datetime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $date            = new \DateTime('now');
        $this->createdAt = $date;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt() : DateTime {
        return $this->createdAt;
    }

    /**
     * @var Insertion
     * @ORM\ManyToOne(targetEntity="Insertion\Models\Insertion", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_id", referencedColumnName="id")
     */
    private $insertion;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }


    /**
     * @return Insertion
     */
    public function getInsertion() : Insertion {
        return $this->insertion;
    }

    /**
     * @param Insertion $insertion
     *
     * @return InsertionUserBookmark
     */
    public function setInsertion(Insertion $insertion) : InsertionUserBookmark {
        $this->insertion = $insertion;
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
     */
    public function setUser($user) : void {
        $this->user = $user;
    }



}
