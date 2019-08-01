<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
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
     * @var Datetime
     * @ORM\Column(name="last_checked", type="datetime")
     */
    private $lastChecked;

    /**
     * @var InsertionType
     * @ORM\ManyToOne(targetEntity="Insertion\Models\InsertionType", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="insertion_type_id", referencedColumnName="id")
     */
    private $insertionType;
    /**
     * @var array
     * @ORM\Column(name="params", type="object")
     */
    private $params;

    /**
     * @ORM\PrePersist
     * @throws Exception
     */
    public function onPrePersist() {
        $date              = new DateTime('now');
        $this->createdAt   = $date;
        $this->lastChecked = $date;
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
     * @return DateTime
     */
    public function getLastChecked() : DateTime {
        return $this->lastChecked;
    }

    /**
     * @return InsertionUserSearchBookmark
     * @throws Exception
     */
    public function setChecked() : InsertionUserSearchBookmark {
        $date              = new DateTime('now');
        $this->lastChecked = $date;

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

    /**
     * @return InsertionType
     */
    public function getInsertionType() : InsertionType {
        return $this->insertionType;
    }

    /**
     * @param InsertionType $insertionType
     */
    public function setInsertionType(InsertionType $insertionType) : void {
        $this->insertionType = $insertionType;
    }

}
