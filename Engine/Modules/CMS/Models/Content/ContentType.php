<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 09:10
 */

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_content_type")
 * @ORM\Entity
 */
class ContentType extends AbstractModel
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var ContentTypeGroup
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="content_type_group_id", referencedColumnName="id")
     */
    private $group;
    
    /**
     * @var string
     * @ORM\Column(name="content_type_name", type="string", nullable=false, unique=true)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="content_type_icon", type="string", nullable=true)
     */
    private $icon;
    
    /**
     * @var string
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private $description;
    
    /**
     * @var string
     * @ORM\Column(name="class_path", type="string", nullable=false)
     */
    private $classPath;
    
    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @return ContentTypeGroup
     */
    public function getGroup(): ?ContentTypeGroup
    {
        return $this->group;
    }
    
    /**
     * @param ContentTypeGroup $group
     * 
     * @return ContentType
     */
    public function setGroup(?ContentTypeGroup $group): ContentType
    {
        $this->group = $group;
        return $this;
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
     * 
     * @return ContentType
     */
    public function setName(string $name): ContentType
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }
    
    /**
     * @param string $icon
     * 
     * @return ContentType
     */
    public function setIcon(?string $icon): ContentType
    {
        $this->icon = $icon;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * @param string $description
     * 
     * @return ContentType
     */
    public function setDescription(string $description): ContentType
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getClassPath(): string
    {
        return $this->classPath;
    }
    
    /**
     * @param string $classPath
     * 
     * @return ContentType
     */
    public function setClassPath(string $classPath): ContentType
    {
        $this->classPath = $classPath;
        return $this;
    }
}