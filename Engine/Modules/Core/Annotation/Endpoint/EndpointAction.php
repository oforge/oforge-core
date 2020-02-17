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
     * @var string|string[]|null $assetBundles
     */
    private $assetBundles;
    /**
     * Mode of how assetsBundles are created for endpoint. AssetBundlesMode::OVERRIDE (default) or AssetBundlesMode::MERGE or AssetBundlesMode::NONE.
     *
     * @var string|null $assetBundles
     */
    private $assetBundlesMode = null;
    /**
     * Route order for this action, overrides Endpoint#order.
     *
     * @var int|null $order Disable order with null.
     */
    private $order;
    /**
     * @var bool $create
     */
    private $create = true;

    /**
     * Endpoint constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->assetBundles     = $config['assetScope'] ?? $config['assetBundles'] ?? null;//TODO remove assetScore after asset refactoring
        $this->assetBundlesMode = $config['assetBundlesMode'] ?? null;

        $this->method = $config['method'] ?? EndpointMethod::ANY;
        $this->name   = $config['name'] ?? '';
        $this->order  = $config['order'] ?? null;
        $this->path   = $config['path'] ?? '';
        $this->create = $config['create'] ?? true;
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
     * @return string|string[]|null
     */
    public function getAssetBundles() {
        return $this->assetBundles;
    }

    /**
     * @return string|null
     */
    public function getAssetBundlesMode() : ?string {
        return $this->assetBundlesMode;
    }

    /**
     * @return int|null
     */
    public function getOrder() : ?int {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isCreate() : bool {
        return $this->create;
    }

}
