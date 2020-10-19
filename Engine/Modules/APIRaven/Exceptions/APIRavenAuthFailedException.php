<?php

namespace Oforge\Engine\Modules\APIRaven\Exceptions;

use Symfony\Component\CssSelector\Exception\InternalErrorException;

class APIRavenAuthFailedException extends InternalErrorException {
    /** @inheritDoc */
    public function __construct($apiUrl) {
        parent::__construct('API auth failed on' );
    }
}
