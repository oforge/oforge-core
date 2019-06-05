<?php

namespace Blog\Exceptions;

use Exception;

/**
 * Class BlogException
 *
 * @package Blog\Exceptions
 */
class BlogException extends Exception {

    /** @inheritDoc */
    public function __construct($message = '') {
        parent::__construct($message);
    }

}
