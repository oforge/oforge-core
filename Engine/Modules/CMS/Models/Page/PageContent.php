<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:38
 */

namespace Oforge\Engine\Modules\CMS\Models\Page;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

/**
 * @ORM\Table(name="oforge_cms_page_content")
 * @ORM\Entity
 */
class PageContent extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var PagePath
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Page\PagePath", inversedBy="pageContent")
     * @ORM\JoinColumn(name="page_path_id", referencedColumnName="id")
     */
    private $pagePath;
    
    /**
     * @var Content
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\Content", inversedBy="content")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    private $content;
    
    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $order;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return PagePath
     */
    public function getPagePath(): ?PagePath
    {
        return $this->pagePath;
    }
    
    /**
     * @param PagePath $pagePath
     * 
     * @return PageContent
     */
    public function setPagePath(?PagePath $pagePath): PageContent
    {
        $this->pagePath = $pagePath;
        return $this;
    }
    
    /**
     * @return Content
     */
    public function getContent(): ?Content
    {
        return $this->content;
    }
    
    /**
     * @param Content $content
     *
     * @return PageContent
     */
    public function setContent(?Content $content): PageContent
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * @param int $order
     * 
     * @return PageContent
     */
    public function setOrder($order): PageContent
    {
        $this->order = $order;
        return $this;
    }
}
