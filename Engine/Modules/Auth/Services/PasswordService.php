<?php

namespace Oforge\Engine\Modules\Auth\Services;

use Exception;
use Oforge\Engine\Modules\Auth\Enums\InvalidPasswordFormatException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;

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
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $password
     *
     * @return PasswordService
     * @throws InvalidPasswordFormatException
     */
    public function validateFormat(string $password) : PasswordService {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        if (empty(trim($password))) {
            throw new InvalidPasswordFormatException(I18N::translate('auth_invalid_password_format_empty', [
                'en' => 'The password cannot be empty or only contain spaces.',
                'de' => 'Das Passwort darf nicht leer sein oder nur aus Leerzeichen bestehen.',
            ]));
        }
        $passwordMinLength = (int) $configService->get('auth_core_password_min_length');
        if (strlen($password) < $passwordMinLength) {
            throw new InvalidPasswordFormatException(sprintf(I18N::translate('auth_invalid_password_format_length', [
                'en' => 'The password must be at least %s characters long.',
                'de' => 'Das Password muss mindestents %s Zeichen lang sein.',
            ]), $passwordMinLength));
        }

        return $this;
    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function validate(string $password, string $hash) : bool {
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
    public function generatePassword($length = 12) : string {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789-=~@#$%^&*()_+,./<>?;:[]{}';
        $str   = '';
        $max   = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[random_int(0, $max)];
        }

        return $str;
    }

}
