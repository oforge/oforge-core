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
     * @var PageContent[]
     * @ORM\OneToMany(targetEntity="Oforge\Engine\Modules\CMS\Models\Page\PageContent", mappedBy="pagePath", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="page_path_id")
     */
    private $pageContent;
    
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
     * 
     * @return PagePath
     */
    public function setPage(?Page $page): PagePath
    {
        $this->page = $page;
        return $this;
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
     * 
     * @return PagePath
     */
    public function setLanguage(?Language $language): PagePath
    {
        $this->language = $language;
        return $this;
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
     * 
     * @return PagePath
     */
    public function setPath(string $path): PagePath
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return PageContent[]
     */
    public function getPageContent()
    {
        return $this->pageContent;
    }

    /**
     * @param PageContent[] $pageContent
     * 
     * @return PagePath
     */
    public function setPageContent(array $pageContent): PagePath
    {
        $this->pageContent = $pageContent;
        return $this;
    }
}
