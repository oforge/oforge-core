<?php

namespace Oforge\Engine\Modules\Core\Annotation\Cache;


/**
 * Class CacheInvalidation
 *
 * @Annotation
 * @Target({"METHOD"})
 * @package Oforge\Engine\Modules\Core\Annotation\Cache
 */
class CacheInvalidation {
    /**
     * Slot.
     *
     * @var string $slot
     */
    private $slot;

    /**
     * CacheInvalidation constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->slot       = $config['slot'] ?? 'default';
    }

    /**
     * @return string
     */
    public function getSlot() : string {
        return $this->slot;
    }
}
