<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 13:12
 */
namespace Oforge\Engine\Modules\UserManagement\Services;

use Oforge\Engine\Modules\Auth\Models\User\User;

class UsersCrudService extends BaseUsersCrudService {
    protected $userModel = User::class;
}