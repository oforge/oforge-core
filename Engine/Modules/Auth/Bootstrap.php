<?php
namespace Oforge\Engine\Modules\Auth;

use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\BackendAuthService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 * @package Oforge\Engine\Modules\Auth
 */
class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            "backend.auth" => BackendAuthService::class
        ];
        
        $this->models = [
            BackendUser::class
        ];
    }
}
