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

/**
 * @ORM\Table(name="oforge_cms_content")
 * @ORM\Entity
 */
class Content extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(name="content_name", type="string", nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="content_data", type="string", nullable=true)
     */
    private $data;
    
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="Content")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * @ORM\Column(name="parent_id", type="integer")
     */
    private $parent;
    
    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="ContentType")
     * @ORM\JoinColumn(name="type", referencedColumnName="id")
     * @ORM\Column(name="type", type="object")
     */
    private $type;
    
    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId(int $id) : void {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name) : void {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getData() : string {
        return $this->data;
    }
    
    /**
     * @param string $data
     */
    public function setData(string $data) : void {
        $this->data = $data;
    }
    
    /**
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }
    
    /**
     * @param string $type
     */
    public function setType(string $type) : void {
        $this->type = $type;
    }
    
    /**
     * @return int
     */
    public function getParent() : int {
        return $this->parent;
    }
    
    /**
     * @param int $parent
     */
    public function setParent(int $parent) : void {
        $this->parent = $parent;
    }
}