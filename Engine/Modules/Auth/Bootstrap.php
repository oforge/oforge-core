<?php
namespace Oforge\Engine\Modules\Auth;

use Oforge\Engine\Modules\Auth\Controller\Backend\LoginController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
#use Oforge\Engine\Modules\Auth\Services\BackendLoginService;
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
        $this->endpoints = [
            "/backend/login" => ["controller" => LoginController::class, "name" => "backend_login"]
        ];
        
        $this->services = [
            "auth" => AuthService::class,#
            #"backend.login" => BackendLoginService::class
        ];
        
        $this->models = [
            BackendUser::class
        ];
    }
}
