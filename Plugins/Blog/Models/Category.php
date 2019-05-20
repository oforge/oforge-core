<?php

namespace Blog\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class Category
 *
 * @package Blog\Models
 * @ORM\Entity
 * @ORM\Table(name="oforge_blog_categories")
 */
class Category extends AbstractModel {
    /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string $name
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;
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
     * @var string $cssClass
     * @ORM\Column(name="css_class", type="string", nullable=false, options={"default":""})
     */
    private $cssClass = '';
    /**
     * @var string|null $headerTitle
     * @ORM\Column(name="header_title", type="string", nullable=true, options={"default":null})
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
     * @var ArrayCollection $posts
     * @ORM\OneToMany(targetEntity="Post", mappedBy="category", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="category_id")
     */
    private $posts;

    public function __construct() {
        $this->posts = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
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
     * @return Category
     */
    public function setLanguage(string $language) : Category {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Category
     */
    public function setName(string $name) : Category {
        $this->name = $name;

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
     * @return Category
     */
    public function setSeoUrlPath(string $seoUrlPath) : Category {
        $this->seoUrlPath = $seoUrlPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getCssClass() : string {
        return $this->cssClass;
    }

    /**
     * @param string $cssClass
     *
     * @return Category
     */
    public function setCssClass(string $cssClass) : Category {
        $this->cssClass = $cssClass;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHeaderTitle() : ?string {
        return $this->headerTitle;
    }

    /**
     * @param string|null $headerTitle
     *
     * @return Category
     */
    public function setHeaderTitle(?string $headerTitle) : Category {
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
     * @return Category
     */
    public function setHeaderSubtext(?string $headerSubtext) : Category {
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
     * @return Category
     */
    public function setHeaderImage(?int $headerImage) : Category {
        $this->headerImage = $headerImage;

        return $this;
    }

    /** @return ArrayCollection */
    public function getPosts() {
        return $this->posts;
    }

}
