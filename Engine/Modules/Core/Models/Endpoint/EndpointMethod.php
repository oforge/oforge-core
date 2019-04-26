<?php

namespace Oforge\Engine\Modules\Core\Models\Endpoint;

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

    /**
     * Check if valid http method.
     *
     * @param string $method
     *
     * @return bool
     */
    public static function isValid(string $method) : bool {
        switch ($method) {
            case self::ANY:
            case self::DELETE:
            case self::GET:
            case self::OPTIONS:
            case self::PATCH:
            case self::POST:
            case self::PUT:
                return true;
            default:
                return false;
        }
    }

}
