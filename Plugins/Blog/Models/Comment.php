<?php

namespace Blog\Models;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class Comment
 *
 * @package Blog\Models
 * @ORM\Entity
 * @ORM\Table(name="oforge_blog_comments")
 * @ORM\HasLifecycleCallbacks
 */
class Comment extends AbstractModel {
    /**
     * @var string $id
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="\Blog\Models\CommentIdGenerator")
     */
    private $id;
    /**
     * @var DateTimeImmutable $created
     * @ORM\Column(name="created", type="datetime_immutable", nullable=false)
     */
    private $created;
    /**
     * @var DateTimeImmutable $language
     * @ORM\Column(name="updated", type="datetime_immutable", nullable=false)
     */
    private $updated;
    /**
     * @var Post $post
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="comments", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id")
     */
    private $post;
    /**
     * @var User $author
     * @ORM\ManyToOne(targetEntity="\FrontendUserManagement\Models\User", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;
    /**
     * @var string $content
     * @ORM\Column(name="content", type="text", nullable=false, options={"default":""})
     */
    private $content = '';

    /** @ORM\PrePersist */
    public function onPrePersist() {
        $now           = new DateTimeImmutable('now');
        $this->created = $now;
        $this->updated = $now;
    }

    /** @ORM\PreUpdate */
    public function onPreUpdate() {
        $this->updated = new DateTimeImmutable('now');
    }

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreated() : DateTimeImmutable {
        return $this->created;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getUpdated() : DateTimeImmutable {
        return $this->updated;
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
     * @return Comment
     */
    protected function setPost(Post $post) : Comment {
        $this->post = $post;

        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor() : User {
        return $this->author;
    }

    /**
     * @param User $author
     *
     * @return Comment
     */
    protected function setAuthor(User $author) : Comment {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent() : string {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return Comment
     */
    public function setContent(string $content) : Comment {
        $this->content = $content;

        return $this;
    }

}
