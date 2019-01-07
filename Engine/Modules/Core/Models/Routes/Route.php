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
     * @var string
     * @ORM\Column(name="asset_scope", type="string", nullable=true, options={"default":"frontend"})
     */
    private $asset_scope = "frontend";
    
    /**
     * @var int
     * @ORM\Column(name="orderby", type="integer", nullable=true)
     */
    private $order;
    
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

    /**
     * @return string
     */
    public function getAssetScope()
    {
        return $this->asset_scope;
    }

    /**
     * @param string $asset_scope
     */
    public function setAssetScope($asset_scope)
    {
        $this->asset_scope = $asset_scope;
    }
    
    /**
     * @return int
     */
    public function getOrder(): int {
        return $this->order;
    }
    
    /**
     * @param int $order
     */
    public function setOrder( int $order ): void {
        $this->order = $order;
    }
}