<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class IllegalTemplateEngineException extends \Exception {
    /**
     * IllegalTemplateEngineException constructor.
     *
     * @param $engineType
     */
    public function __construct( $engineType ) {
        parent::__construct( "Config key $engineType exists but the call to $engineType is illegal." );
    }
}