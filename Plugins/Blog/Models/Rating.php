<?php

namespace Blog\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class Rating
 *
 * @package Blog\Models
 * @ORM\Entity
 * @ORM\Table(name="oforge_blog_ratings")
 */
class Rating extends AbstractModel {
    /**
     * @var string $id
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Blog\Models\RatingIdGenerator")
     */
    private $id;
    /**
     * @var int $postID
     * @ORM\Column(name="post_id", type="integer", nullable=false)
     */
    private $postID;
    /**
     * @var int $userID
     * @ORM\Column(name="user_id", type="integer", nullable=false)
     */
    private $userID;
    /**
     * @var bool $rating
     * @ORM\Column(name="rating", type="boolean", nullable=false)
     */
    private $rating;

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPostID() : int {
        return $this->postID;
    }

    /**
     * @param int $postID
     *
     * @return Rating
     */
    protected function setPostID(int $postID) : Rating {
        $this->postID = $postID;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserID() : int {
        return $this->userID;
    }

    /**
     * @param int $userID
     *
     * @return Rating
     */
    protected function setUserID(int $userID) : Rating {
        if (!isset($this->userID)) {
            $this->userID = $userID;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isRating() : bool {
        return $this->rating;
    }

    /**
     * @param bool $rating
     *
     * @return Rating
     */
    public function setRating(bool $rating) : Rating {
        $this->rating = $rating;

        return $this;
    }

}
