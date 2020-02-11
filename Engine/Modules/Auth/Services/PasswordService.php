<?php

namespace Oforge\Engine\Modules\Auth\Services;

use Exception;

/**
 * Class PasswordService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class PasswordService {

    /**
     * @param string $password
     *
     * @return bool|string
     */
    public function hash(string $password) {
        if (strlen($password) > 5) {
            return password_hash($password, PASSWORD_BCRYPT);
        }

        return null;
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function validate(string $password, string $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Create a password
     *
     * @param int $length
     *
     * @return string
     * @throws Exception
     */
    public function generatePassword($length = 12) {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789-=~@#$%^&*()_+,./<>?;:[]{}';
        $str   = '';
        $max   = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $str;
    }

}
