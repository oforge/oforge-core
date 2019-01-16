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
class ContentType extends AbstractModel {
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="ContentType", inversedBy="child_types")
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
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
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="ContentType", mappedBy="id")
     * @ORM\Column(name="child_types", type="array", nullable=true)
     */
    private $childTypes;
    
    public function __construct() {
        $this->childTypes = new ArrayCollection();
    }
    
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
    public function getDescription() : string {
        return $this->description;
    }
    
    /**
     * @param string $description
     */
    public function setDescription(string $description) : void {
        $this->description = $description;
    }
    
    /**
     * @return string
     */
    public function getClassPath() : string {
        return $this->classPath;
    }
    
    /**
     * @param string $classPath
     */
    public function setClassPath(string $classPath) : void {
        $this->classPath = $classPath;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getChildTypes() : ArrayCollection {
        return $this->childTypes;
    }
    
    /**
     * @param ArrayCollection $childTypes
     */
    public function setChildTypes(ArrayCollection $childTypes) : void {
        $this->childTypes = $childTypes;
    }
    
    /**
     * @return string
     */
    public function getIcon() : ?string {
        return $this->icon;
    }
    
    /**
     * @param string $icon
     */
    public function setIcon(string $icon) : void {
        $this->icon = $icon;
    }
}