<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:01
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Exception;
use Firebase\JWT\JWT;

/**
 * Class AuthService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class AuthService {

    /**
     * Create a JSON Web Token
     *
     * @param array $userData
     *
     * @return string
     */
    public function createJWT(array $userData) : string {
        $salt = Oforge()->Settings()->get("jwt_salt");
        $jwt  = JWT::encode($userData, $salt, 'HS512');

        return $jwt;
    }

    /**
     * Check, if the current Token is valid and belongs to the requested userID.
     *
     * @param array $userData
     * @param string $jwt
     *
     * @return bool
     */
    public function hasValidToken(array $userData, string $jwt) : bool {
        $salt = Oforge()->Settings()->get("jwt_salt");
        try {
            $decoded = JWT::decode($jwt, $salt, ['HS512']);

            $array = $decoded;
            if (isset($array['user'])) {
                return $array == $userData;
            }
        } catch (Exception $e) {
            Oforge()->Logger()->get("system")->addWarning($e->getMessage(), ["exception" => $e]);
        }

        return false;
    }

    /**
     * Check if the token is valid (can be decoded)
     *
     * @param string $jwt
     *
     * @return bool
     */
    public function isValid(string $jwt) {
        /**
         * TODO: add more information into the Token for better validation, e.g. created-timestamp
         */
        $decode = $this->decode($jwt);

        return isset($decode);
    }

    /**
     * decode the token
     *
     * @param string $jwt
     *
     * @return array|null
     */
    public function decode(?string $jwt) {
        if (!isset($jwt)) {
            return null;
        }
        $salt = Oforge()->Settings()->get("jwt_salt");
        try {
            $decoded = JWT::decode($jwt, $salt, ['HS512']);

            return (array) $decoded;
        } catch (Exception $exception) {
            Oforge()->Logger()->get('system')->addWarning($exception->getMessage(), ["exception" => $exception]);
        }

        return null;
    }
}
