<?php

namespace Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator;

/**
 * @Annotation
 */
final class DiscriminatorEntry {
    /**
     * @var string|null $value
     */
    private $value;

    /**
     * DiscriminatorEntry constructor.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->value = $config['value'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getValue() {
        return $this->value;
    }

}
