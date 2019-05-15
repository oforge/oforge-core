<?php

namespace Blog\Models;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class Post
 *
 * @package Blog\Models
 * @ORM\Entity
 * @ORM\Table(name="oforge_blog_posts")
 * @ORM\HasLifecycleCallbacks
 */
class Post extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
     * @var string $language
     * @ORM\Column(name="language", type="string", nullable=false)
     */
    private $language;
    /**
     * @var string $seoUrlPath
     * @ORM\Column(name="seo_url_path", type="string", nullable=false)
     */
    private $seoUrlPath;
    /**
     * @var BackendUser $author
     * @ORM\OneToOne(targetEntity="\Oforge\Engine\Modules\Auth\Models\User\BackendUser")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     */
    private $author;
    /**
     * @var string $headerTitle
     * @ORM\Column(name="header_title", type="string", nullable=false, options={"default":null})
     */
    private $headerTitle = null;
    /**
     * @var string|null $headerSubtext
     * @ORM\Column(name="header_subtext", type="text", nullable=true, options={"default":null})
     */
    private $headerSubtext = null;
    /**
     * @var int|null $headerImage
     * @ORM\Column(name="header_image", type="integer", nullable=true, options={"default":null})
     */
    private $headerImage = null;
    /**
     * @var string $excerpt
     * @ORM\Column(name="excerpt", type="text", nullable=false, options={"default":""})
     */
    private $excerpt = '';
    /**
     * @var string $content
     * @ORM\Column(name="content", type="text", nullable=false, options={"default":""})
     */
    private $content = '';
    /**
     * @var Category $category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="posts", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;
    /**
     * @var Rating[] $ratings
     * @ORM\OneToMany(targetEntity="Rating", mappedBy="post", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     */
    private $ratings;
    /**
     * @var ArrayCollection $comments
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="post_id")
     * @ORM\OrderBy({"created" = "DESC"})
     */
    private $comments;

    public function __construct() {
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps() {
        $now           = new DateTimeImmutable('now');
        $this->updated = $now;
        if (!isset($this->created)) {
            $this->created = $now;
        }
    }

    /**
     * @return int
     */
    public function getId() : int {
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
     * @return string
     */
    public function getLanguage() : string {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return Post
     */
    public function setLanguage(string $language) : Post {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getSeoUrlPath() : string {
        return $this->seoUrlPath;
    }

    /**
     * @param string $seoUrlPath
     *
     * @return Post
     */
    public function setSeoUrlPath(string $seoUrlPath) : Post {
        $this->seoUrlPath = $seoUrlPath;

        return $this;
    }

    /**
     * @return BackendUser
     */
    public function getAuthor() : BackendUser {
        return $this->author;
    }

    /**
     * @param BackendUser $author
     *
     * @return Post
     */
    public function setAuthor(BackendUser $author) : Post {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderTitle() : string {
        return $this->headerTitle;
    }

    /**
     * @param string $headerTitle
     *
     * @return Post
     */
    public function setHeaderTitle(string $headerTitle) : Post {
        $this->headerTitle = $headerTitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeaderSubtext() : ?string {
        return $this->headerSubtext;
    }

    /**
     * @param string|null $headerSubtext
     *
     * @return Post
     */
    public function setHeaderSubtext(?string $headerSubtext) : Post {
        $this->headerSubtext = $headerSubtext;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHeaderImage() : ?int {
        return $this->headerImage;
    }

    /**
     * @param int|null $headerImage
     *
     * @return Post
     */
    public function setHeaderImage(?int $headerImage) : Post {
        $this->headerImage = $headerImage;

        return $this;
    }

    /**
     * @return string
     */
    public function getExcerpt() : string {
        return $this->excerpt;
    }

    /**
     * @param string $excerpt
     *
     * @return Post
     */
    public function setExcerpt(string $excerpt) : Post {
        $this->excerpt = $excerpt;

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
     * @return Post
     */
    public function setContent(string $content) : Post {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory() : Category {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return Post
     */
    public function setCategory(Category $category) : Post {
        $this->category = $category;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments() : ArrayCollection {
        return $this->comments;
    }

    /**
     * @return Rating[]
     */
    public function getRatings() : array {
        return $this->ratings;
    }

}
