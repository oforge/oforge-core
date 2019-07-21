<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 13:12
 */
namespace Oforge\Engine\Modules\UserManagement\Services;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;

class BackendUsersCrudService extends BaseUsersCrudService {
    protected $userModel = BackendUser::class;
}