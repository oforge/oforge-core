<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:01
 */

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\User;

/**
 * Class FrontendUserService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class FrontendUserService {

    public function getUser() : ?User {
        /** @var $authService AuthService */
        $authService = Oforge()->Services()->get("auth");
        $user        = $authService->decode($_SESSION["auth"]);

        if (isset($user) && isset($user['id']) && $user['type'] == User::class) {
            /** @var $userService UserService */
            $userService = Oforge()->Services()->get("frontend.user.management.user");

            return $userService->getUserById($user['id']);
        }

        return null;
    }
}
