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
     * @var string
     * @ORM\Column(name="path", type="string", nullable=false)
     */
    private $path;

    /**
     * @var int
     * @ORM\OneToOne(targetEntity="Oforge\Engine\Modules\I18n\Models\Language")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     * @ORM\Column(name="language_id", type="integer")
     */
    private $language;
    
    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;
    
    /**
     * @var PageContent[]
     * @ORM\OneToMany(targetEntity="PageContent", mappedBy="pagePath", cascade={"all"})
     * @ORM\JoinColumn(name="id", referencedColumnName="page_path_id")
     */
    private $pageContent;
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getLanguage(): int
    {
        return $this->language;
    }

    /**
     * @param int $language
     */
    public function setLanguage(int $language): void
    {
        $this->language = $language;
    }
    
    /**
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }
    
    /**
     * @param string $path
     */
    public function setPath(string $path) : void {
        $this->path = $path;
    }
    
    /**
     * @return Page
     */
    public function getPage() : Page {
        return $this->page;
    }
    
    /**
     * @param Page $page
     */
    public function setPage(Page $page) : void {
        $this->page = $page;
    }
    
    /**
     * @return PageContent[]
     */
    public function getPageContent()  {
        return $this->pageContent;
    }
    
    /**
     * @param PageContent[] $pageContent
     */
    public function setPageContent(array $pageContent) : void {
        $this->pageContent = $pageContent;
    }
}
