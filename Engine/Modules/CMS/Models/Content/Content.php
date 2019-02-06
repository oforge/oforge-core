<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 09:26
 */

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\CMS\Models\Page\Page;

/**
 * @ORM\Table(name="oforge_cms_content")
 * @ORM\Entity
 */
class Content extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Page\Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     */
    private $page;
    
    /**
     * @var ContentType
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentType")
     * @ORM\JoinColumn(name="content_type_id", referencedColumnName="id")
     */
    private $type;
    
    /**
     * @var int
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parent;
    
    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer", nullable=true)
     */
    private $sort_order;
    
    /**
     * @var string
     * @ORM\Column(name="content_name", type="string", nullable=false)
     */
    private $name;
    
    /**
     * @var mixed
     * @ORM\Column(name="content_configuration", type="object", nullable=true)
     */
    private $configuration;
    
    /**
     * @return int
     */
    public function getId(): ?int {
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
     * @return ContentType
     */
    public function getType(): ?ContentType
    {
        return $this->type;
    }
    
    /**
     * @param ContentType $type
     */
    public function setType(?ContentType $type)
    {
        $this->type = $type;
    }
    
    /**
     * @return int
     */
    public function getParent(): int
    {
        return $this->parent;
    }
    
    /**
     * @param int $parent
     */
    public function setParent(int $parent)
    {
        $this->parent = $parent;
    }
    
    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sort_order;
    }
    
    /**
     * @param int $order
     */
    public function setSortOrder(int $sort_order)
    {
        $this->sort_order = $sort_order;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
    
    /**
     * @param mixed $data
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }
}