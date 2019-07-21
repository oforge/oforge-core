<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\Auth\Services;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;

/**
 * Class BackendLoginService
 *
 * @package Oforge\Engine\Modules\Auth\Services
 */
class BackendLoginService extends BaseLoginService {
    /**
     * BackendAuthService constructor.
     */
    public function __construct() {
        parent::__construct(BackendUser::class);
    }

}
