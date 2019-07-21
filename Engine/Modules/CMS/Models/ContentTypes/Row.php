<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 09:10
 */

namespace Oforge\Engine\Modules\CMS\Models\ContentTypes;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

/**
 * @ORM\Table(name="oforge_cms_content_type_row")
 * @ORM\Entity
 */
class Row extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var int
     * @ORM\Column(name="row_id", type="integer")
     */
    private $row;
    
    /**
     * @var Content
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\Content")
     * @ORM\JoinColumn(name="content_id", referencedColumnName="id")
     */
    private $content;
    
    /**
     * @var int
     * @ORM\Column(name="sort_order", type="integer")
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
     * @return int
     */
    public function getRow(): int
    {
        return $this->row;
    }
    
    /**
     * @param int
     *
     * @return Row
     */
    public function setRow(int $row): Row
    {
        $this->row = $row;
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
     * @param Content
     *
     * @return Row
     */
    public function setContent(?Content $content): Row
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
    
    /**
     * @param int $order
     * 
     * @return Row
     */
    public function setOrder(int $order): Row
    {
        $this->order = $order;
        return $this;
    }
}