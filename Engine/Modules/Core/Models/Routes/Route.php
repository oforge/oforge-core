<?php
namespace Oforge\Engine\Modules\Core\Models\Routes;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
/**
 * @ORM\Table(name="oforge_core_routes")
 * @ORM\Entity
 */
class Route extends AbstractModel
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
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="controller", type="string", nullable=false)
     */
    private $controller;

    /**
     * @var string
     * @ORM\Column(name="path", type="string", nullable=false)
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(name="language_id", type="string", nullable=true)
     */
    private $language_id;

    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", nullable=true, options={"default":false})
     */
    private $active = false;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Route
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

        /**
     * Set controller
     *
     * @param string $controller
     *
     * @return Route
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }
    
    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }    
    
    /**
    * Set path
    *
    * @param string $path
    *
    * @return Route
    */
   public function setPath($path)
   {
       $this->path = $path;
       return $this;
   }
   
   /**
    * Get path
    *
    * @return string
    */
   public function getPath()
   {
       return $this->path;
   }

    /**
     * Get language id
     *
     * @return string
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }
       /**
     * Set name
     *
     * @param string $name
     *
     * @return Route
     */
    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;
        return $this;
    }

   
    /**
     * @param bool $active
     *
     * @return Route
     */
    public function setActivate($active)
    {
        $this->active = $active;
        return $this;
    }
    /**
     * @return bool
     */
    public function getActivate()
    {
        return $this->active;
    }
}