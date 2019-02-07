<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 15.01.2019
 * Time: 10:38
 */

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_content_localization")
 * @ORM\Entity
 */
class ContentLocalization extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var Content
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\Content")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    private $content;
    
    /**
     * @var PagePath
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Page\PagePath", inversedBy="$content_localization")
     * @ORM\JoinColumn(name="page_path_id", referencedColumnName="id")
     */
    private $page_path;
    
    /**
     * @var mixed
     * @ORM\Column(name="content_data", type="object", nullable=true)
     */
    private $data;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
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
     */
    public function setContent(?Content $content)
    {
        $this->content = $content;
    }
    
    /**
     * @return PagePath
     */
    public function getPagePath(): ?PagePath
    {
        return $this->page_path;
    }
    
    /**
     * @param PagePath $pagePath
     */
    public function setPagePath(?PagePath $page_path)
    {
        $this->page_path = $page_path;
    }
    
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
