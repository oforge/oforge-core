<?php

namespace Oforge\Engine\Modules\APIRaven\Exceptions;

use Symfony\Component\CssSelector\Exception\InternalErrorException;

class APIRavenInvalidAuthMethodException extends InternalErrorException {
    /** @inheritDoc */
    public function __construct($authMethod) {
        parent::__construct('Invalid auth method: ' . $authMethod);
    }
}
