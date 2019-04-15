<?php

namespace Oforge\Engine\Modules\Core\Annotation\Endpoint;

/**
 * Class EndpointMethod
 *
 * @package Oforge\Engine\Modules\Core\Annotation\Endpoint
 */
class EndpointMethod {
    public const ANY     = 'any';
    public const GET     = 'get';
    public const POST    = 'post';
    public const PUT     = 'put';
    public const PATCH   = 'patch';
    public const DELETE  = 'delete';
    public const OPTIONS = 'options';

    private function __construct() {
    }

}
