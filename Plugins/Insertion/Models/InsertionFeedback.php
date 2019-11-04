<?php

namespace Insertion\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * @ORM\Table(name="oforge_insertion_feedback")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class InsertionFeedback extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="FrontendUserManagement\Models\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     * @ORM\Column(name="rating", type="integer", nullable=false)
     */
    private $rating;

    /**
     * @var string
     * @ORM\Column(name="text", type="string", nullable=false)
     */
    private $text;

    /**
     * @return int
     */
    public function getId() : ?int {
        return $this->id;
    }

    /**
     * @param User|null $user
     *
     * @return InsertionFeedback
     */
    public function setUser(?User $user) : InsertionFeedback {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser() :?User {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getRating() : int {
        return $this->rating;
    }

    /**
     * @param int $rating
     * @return InsertionFeedback
     */
    public function setRating(int $rating) : InsertionFeedback {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string
     */
    public function getText() : string {
        return $this->text;
    }

    /**
     * @param string $text
     * @return InsertionFeedback
     */
    public function setText(string $text) : InsertionFeedback {
        $this->text = $text;

        return $this;
    }
}

