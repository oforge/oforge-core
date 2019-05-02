<?php

namespace Oforge\Engine\Modules\Core\Annotation\Endpoint;

use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;

/**
 * Class EndpointAction
 *
 * @Annotation
 * @Target({"METHOD"})
 * @package Oforge\Engine\Modules\Core\Annotation\Endpoint
 */
class EndpointAction {
    /**
     * Relative url path to EndpointClass#path (prefixed by EndpointClass#path).
     *
     * @var string $path
     */
    private $path;
    /**
     * Route name (prefixed by EndpointClass#name).
     *
     * @var string $name
     */
    private $name;
    /**
     * Route HTML method. Value or array of EndpointMethod constants.
     *
     * @var string $method
     */
    private $method;
    /**
     * Optional asset scope for this action, overrides EndpointClass#assetScope.
     *
     * @var string|null $assetScope
     */
    private $assetScope;
    /**
     * Route order for this action, overrides Endpoint#order.
     *
     * @var int|null $order Disable order with null.
     */
    private $order;

    /**
     * Endpoint constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->assetScope = $config['assetScope'] ?? null;
        $this->method     = $config['method'] ?? EndpointMethod::ANY;
        $this->name       = $config['name'] ?? '';
        $this->order      = $config['order'] ?? null;
        $this->path       = $config['path'] ?? '';
    }

    /**
     * @return string
     */
    public function getPath() : string {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName() : string {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @return string|null
     */
    public function getAssetScope() : ?string {
        return $this->assetScope;
    }

    /**
     * @return int|null
     */
    public function getOrder() : ?int {
        return $this->order;
    }

}
