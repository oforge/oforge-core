<?php

namespace Oforge\Engine\Modules\Core\Models;

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
     * @param array $data
     */
    public function __construct(array $data) {
        $this->value = $data['value'] ?? null;
    }

    /**
     * @return string|null
     */
    public function getValue() {
        return $this->value;
    }
}
