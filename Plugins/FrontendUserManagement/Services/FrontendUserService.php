<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:01
 */

namespace FrontendUserManagement\Services;

use Exception;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;

/**
 * Class FrontendUserService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class FrontendUserService {

    /**
     * @return bool
     */
    public function isLoggedIn() : bool {
        if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
            return true;
        }

        if ($this->getUser() != null) {
            $_SESSION['user_logged_in'] = true;

            return true;
        }

        return false;
    }

    /**
     * @return User|null
     */
    public function getUser() : ?User {
        try {
            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode(ArrayHelper::get($_SESSION, 'auth'));
            if (isset($user) && isset($user['id']) && $user['type'] === User::class) {
                /** @var UserService $userService */
                $userService = Oforge()->Services()->get('frontend.user.management.user');

                return $userService->getUserById($user['id']);
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return null;
    }

    public function getUserById($userId) {
    }

}
