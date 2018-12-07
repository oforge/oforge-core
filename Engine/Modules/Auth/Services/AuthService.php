<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:01
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Firebase\JWT\JWT;

/**
 * Class AuthService
 * @package Oforge\Engine\Modules\Auth\Services
 */
class AuthService {
    /**
     * Create a JSON Web Token
     *
     * @param array $user
     *
     * @return string
     */
    public function createJWT(array $user) : string {
        $key = Oforge()->Settings()->get("jwt_salt");
        $jwt = JWT::encode($user, $key, 'HS512');
        return $jwt;
    }
    
    /**
     * Check, if the current Token is valid and belongs to the requested userId
     *
     * @param array $user
     * @param string $jwt
     *
     * @return bool
     */
    public function hasValidToken(array $user, string $jwt) : bool {
        $key = Oforge()->Settings()->get("jwt_salt");
        try {
            $decoded = JWT::decode($jwt, $key, ['HS512']);
            $arr = (array)$decoded;
            if (array_key_exists('user', $arr)) {
                return $arr == $user;
            }
        } catch (\Exception $e) {
            Oforge()->Logger()->get("system")->addWarning($e->getMessage(), ["exception" => $e]);
        }
        return false;
    }
}
