<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 09:10
 */

namespace Oforge\Engine\Modules\CMS\Models\Content;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_cms_content_type")
 * @ORM\Entity
 */
class ContentType extends AbstractModel
{
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="child_types")
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var ContentTypeGroup
     * @ORM\ManyToOne(targetEntity="Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup")
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
    private $class_path;
    
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
     */
    public function setGroup(?ContentTypeGroup $group)
    {
        $this->group = $group;
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
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }
    
    /**
     * @param string $icon
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
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
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }
    
    /**
     * @return string
     */
    public function getClassPath(): string
    {
        return $this->class_path;
    }
    
    /**
     * @param string $classPath
     */
    public function setClassPath(string $class_path)
    {
        $this->class_path = $class_path;
    }
}