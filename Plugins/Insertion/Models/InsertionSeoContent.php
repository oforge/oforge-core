<?php
namespace Insertion\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Seo\Models\SeoUrl;

/**
 * @ORM\Table(name="oforge_insertion_seo_content")
 * @ORM\Entity
 */
class InsertionSeoContent extends AbstractModel {
   /**
     * @var int $id
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var SeoUrl $seoTargetUrl
     * @ORM\OneToOne(targetEntity="Seo\Models\SeoUrl")
     * @ORM\JoinColumn(name="seo_target_id", referencedColumnName="id")
     */
    private $seoTargetUrl;

    /**
     * @var string|null $metaTitle
     * @ORM\Column(name="meta_title", type="string", nullable=true)
     */
    private $metaTitle;

    /**
     * @var string|null $metaDescription
     * @ORM\Column(name="meta_description", type="string", nullable=true)
     */
    private $metaDescription;

    /**
     * @var string|null $contentElements
     * @ORM\Column(name="content_elements", type="string", nullable=true)
     */
    private $contentElements;

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return SeoUrl
     */
    public function getSeoTargetUrl() : SeoUrl {
        return $this->seoTargetUrl;
    }

    /**
     * @param SeoUrl $seoTargetUrl
     *
     * @return InsertionSeoContent
     */
    public function setSeoTargetUrl(SeoUrl $seoTargetUrl) : InsertionSeoContent {
        $this->seoTargetUrl = $seoTargetUrl;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaTitle() : ?string {
        return $this->metaTitle;
    }

    /**
     * @param string|null $metaTitle
     *
     * @return InsertionSeoContent
     */
    public function setMetaTitle(?string $metaTitle) : InsertionSeoContent {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMetaDescription() : ?string {
        return $this->metaDescription;
    }

    /**
     * @param string|null $metaDescription
     *
     * @return InsertionSeoContent
     */
    public function setMetaDescription(?string $metaDescription) : InsertionSeoContent {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getContentElements() : ?string {
        return $this->contentElements;
    }

    /**
     * @param string|null $contentElements
     *
     * @return InsertionSeoContent
     */
    public function setContentElements(?string $contentElements) : InsertionSeoContent {
        $this->contentElements = $contentElements;

        return $this;
    }

}
