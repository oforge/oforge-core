<?php

namespace Oforge\Engine\Modules\Core\Services;

/**
 * Class TokenService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class TokenService {
    public const  DEFAULT_ACTION = 'oforge';
    private const KEY            = 'token';

    /**
     * @param string $action
     *
     * @return string
     */
    public function get(string $action = self::DEFAULT_ACTION) : string {
        if (!isset($_SESSION[self::KEY][$action]) || empty($_SESSION[self::KEY])) {
            $this->renew($action);
        }

        return $_SESSION[self::KEY][$action];
    }

    /**
     * @param string|null $token
     * @param string $action
     *
     * @return bool
     */
    public function isValid(?string $token, string $action = self::DEFAULT_ACTION) : bool {
        if (empty($token) || !isset($_SESSION[self::KEY][$action]) || empty($_SESSION[self::KEY][$action])) {
            return false;
        }

        return hash_equals($_SESSION[self::KEY][$action], $token);
    }

    /**
     * @param string $action
     */
    public function renew(string $action = self::DEFAULT_ACTION) {
        if (!isset($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = [];
        }
        $_SESSION[self::KEY][$action] = bin2hex(random_bytes(32));
    }

    /**
     * @param string $action
     */
    public function clear(string $action = self::DEFAULT_ACTION) {
        unset($_SESSION[self::KEY][$action]);
    }

}
