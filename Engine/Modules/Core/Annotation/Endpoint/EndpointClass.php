<?php

namespace Oforge\Engine\Modules\Core\Annotation\Endpoint;

use Doctrine\Common\Annotations\AnnotationException;

/**
 * Class EndpointClass
 *
 * @Annotation
 * @Target({"CLASS"})
 * @package Oforge\Engine\Modules\Core\Annotation\Endpoint
 */
class EndpointClass {
    /**
     * Base url path. (Required)
     *
     * @var string $path
     */
    private $path;
    /**
     * Global asset scope for this class. Overridable by Endpoint#assetScope. Default=Frontend.
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
     * Optional route name prefix (suffixed by Endpoint#name).
     *
     * @var string $name
     */
    private $name;
    /**
     * Optional global route order. Overridable by Endpoint#order.
     *
     * @var int|null $order
     */
    private $order;
    /**
     * Optional strict Action-suffixed-action-method mode.
     *
     * @var bool $strictActionSuffix
     */
    private $strictActionSuffix;

    /**
     * EndpointClass constructor.
     *
     * @param array $config
     *
     * @throws AnnotationException
     */
    public function __construct(array $config) {
        $this->assetBundles     = $config['assetScope'] ?? $config['assetBundles'] ?? null;//TODO remove assetScore after asset refactoring
        $this->assetBundlesMode = $config['assetBundlesMode'] ?? null;

        $this->name  = $config['name'] ?? '';
        $this->order = $config['order'] ?? null;
        $this->path  = $config['path'] ?? '';

        $this->strictActionSuffix = $config['strictActionSuffix'] ?? true;
    }

    /**
     * @param string $class
     *
     * @throws AnnotationException
     */
    public function checkRequired(string $class) {
        if (!isset($this->path)) {
            throw new AnnotationException (sprintf('Required attribute "%s" of %s is null.', 'path', $class));
        }
    }

    /**
     * @return string
     */
    public function getPath() : string {
        return $this->path;
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
     * @return string
     */
    public function getName() : string {
        return $this->name;
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
    public function isStrictActionSuffix() : bool {
        return $this->strictActionSuffix;
    }

}
