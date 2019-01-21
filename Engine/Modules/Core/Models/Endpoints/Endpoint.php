<?php

namespace Oforge\Engine\Modules\Core\Models\Endpoints;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_core_endpoints")
 * @ORM\Entity
 */
class Endpoint extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var bool
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":false})
     */
    private $active = false;
    /**
     * @var string
     * @ORM\Column(name="name", type="string", nullable=false)
     */
    private $name;
    /**
     * @var string
     * @ORM\Column(name="path", type="string", nullable=false)
     */
    private $path;
    /**
     * @var string
     * @ORM\Column(name="controller", type="string", nullable=false)
     */
    private $controller;
    /**
     * @var string
     * @ORM\Column(name="http", type="string", nullable=false, options={"default":"any"})
     */
    private $httpMethod = 'any';
    /**
     * @var string
     * @ORM\Column(name="asset_scope", type="string", nullable=true, options={"default":"frontend"})
     */
    private $assetScope = 'frontend';
    
    /**
     * @var int
     * @ORM\Column(name="orderby", type="integer", nullable=false, options={"default":1337})
     */
    private $order;
    
    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }
    
    /**
     * @param int $id
     *
     * @return Endpoint
     */
    public function setId( int $id ): Endpoint {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->active;
    }
    
    /**
     * @param bool $active
     *
     * @return Endpoint
     */
    public function setActive( bool $active ): Endpoint {
        $this->active = $active;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
    
    /**
     * @param string $name
     *
     * @return Endpoint
     */
    public function setName( string $name ): Endpoint {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }
    
    /**
     * @param string $path
     *
     * @return Endpoint
     */
    public function setPath( string $path ): Endpoint {
        $this->path = $path;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getController(): string {
        return $this->controller;
    }
    
    /**
     * @param string $controller
     *
     * @return Endpoint
     */
    public function setController( string $controller ): Endpoint {
        $this->controller = $controller;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getHttpMethod(): string {
        return $this->httpMethod;
    }
    
    /**
     * @param string $httpMethod
     *
     * @return Endpoint
     */
    public function setHttpMethod( string $httpMethod ): Endpoint {
        $this->httpMethod = $httpMethod;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAssetScope() {
        return $this->assetScope;
    }
    
    /**
     * @param string $assetScope
     *
     * @return Endpoint
     */
    public function setAssetScope( string $assetScope ): Endpoint {
        $this->assetScope = $assetScope;
        
        return $this;
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
