<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

use Exception;

    /**
 * Class NotFoundException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class NotFoundException extends Exception {

    /**
     * NotFoundException constructor.
     *
     * @param string $text
     */
    public function __construct(string $text) {
        parent::__construct( $text );
    }

}
