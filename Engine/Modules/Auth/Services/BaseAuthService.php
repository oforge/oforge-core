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
 * Class BaseAuthService
 * @package Oforge\Engine\Modules\Auth\Services
 */
class BaseAuthService {
    /**
     * @param string $clientPassword
     * @param string $serverPassword
     *
     * @return bool
     */
    public function comparePasswords(string $clientPassword, string $serverPassword) : bool {
        return password_verify($clientPassword, $serverPassword);
    }
    
    /**
     * Create a JSON Web Token
     *
     * @param int $userId
     *
     * @return string
     */
    public function createJWT(int $userId) : string {
        $key = Oforge()->Settings()->get("jwt_salt");
        $jwt = JWT::encode(['user_id' => $userId], $key, 'HS512');
        return $jwt;
    }
    
    /**
     * Check, if the current Token is valid and belongs to the requested userId
     *
     * @param int $userId
     * @param string $jwt
     *
     * @return bool
     */
    public function hasValidToken(int $userId, string $jwt) : bool {
        $key = Oforge()->Settings()->get("jwt_salt");
        try {
            $decoded = JWT::decode($jwt, $key, ['HS512']);
            $arr = (array)$decoded;
            if (array_key_exists('user_id', $arr)) {
                return $arr['user_id'] == $userId;
            }
        } catch (\Exception $e) {
            Oforge()->Logger()->get("system")->addWarning($e->getMessage(), ["exception" => $e]);
        }
        return false;
    }
}
