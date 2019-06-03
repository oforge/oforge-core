<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * @ORM\Table(name="oforge_insertion_user_search_bookmarks")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionUserSearchBookmark extends AbstractModel {

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
     * @var array
     * @ORM\Column(name="params", type="object")
     */
    private $params;

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $date            = new \DateTime('now');
        $this->createdAt = $date;
    }

    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt() : DateTime {
        return $this->createdAt;
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
     * @return array
     */
    public function getParams() : ?array {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return InsertionUserSearchBookmark
     */
    public function setParams(array $params) : InsertionUserSearchBookmark {
        $this->params = $params;
        return $this;
    }

}
