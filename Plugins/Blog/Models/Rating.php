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
     * @ORM\CustomIdGenerator(class="Blog\Models\RatingIdGenerator")
     */
    private $id;
    /**
     * @var Post $post
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="ratings", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;
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
     * @return Post
     */
    public function getPost() : Post {
        return $this->post;
    }

    /**
     * @param Post $post
     *
     * @return Rating
     */
    protected function setPost(Post $post) : Rating {
        $this->post = $post;

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
