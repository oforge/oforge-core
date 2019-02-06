<?php
/**
 * Created by PhpStorm.
 * User: wootnuss
 * Date: 15.01.2019
 * Time: 17:01
 */

namespace Oforge\Engine\Modules\CMS\Models\Page;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\CMS\Models\Content\ContentLocalization;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * @ORM\Table(name="oforge_cms_page_path")
 * @ORM\Entity
 */
class PagePath extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Page\Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;
    
    /**
     * @var Language
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\I18n\Models\Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    private $language;
    
    /**
     * @var string
     * @ORM\Column(name="path", type="string", nullable=false)
     */
    private $path;
    
    /**
     * @var ContentLocalization[]
     * @ORM\OneToMany(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentLocalization", mappedBy="page_path", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="page_path_id")
     */
    private $content_localization;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return Page
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }
    
    /**
     * @param Page $page
     */
    public function setPage(?Page $page)
    {
        $this->page = $page;
    }
    
    /**
     * @return Language
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(?Language $language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return PagePath
     */
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return ContentLocalization[]
     */
    public function getContentLocalization()
    {
        return $this->content_localization;
    }

    /**
     * @param ContentLocalization[] $contentLocalization
     */
    public function setContentLocalization(array $content_localization)
    {
        $this->content_localization = $content_localization;
    }
}
