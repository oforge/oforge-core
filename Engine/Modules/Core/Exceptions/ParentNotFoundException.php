<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

/**
 * Class ParentNotFoundException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class ParentNotFoundException extends \Exception {
    /**
     * ConfigElementNotFoundException constructor.
     *
     * @param string $parentName
     */
    public function __construct(string $parentName)
    {
        parent::__construct("Parent element with name '$parentName' not found");
    }
}
