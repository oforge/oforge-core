<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 09:54
 */
namespace Oforge\Engine\Modules\UserManagement;

use Oforge\Engine\Modules\AdminBackend\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\UserManagement\Controller\Backend\ProfileController;
use Oforge\Engine\Modules\UserManagement\Controller\Backend\UserManagementController;
use Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/backend/users" => [
                "controller" => UserManagementController::class,
                "name" => "backend_users",
                "asset_scope" => "Backend"
            ],
            "/backend/profile" => [
                "controller" => ProfileController::class,
                "name" => "backend_profile",
                "asset_scope" => "Backend"
            ]
        ];
        
        $this->dependencies = [
            \Oforge\Engine\Modules\CRUD\Bootstrap::class,
            \Oforge\Engine\Modules\Auth\Bootstrap::class
        ];
        
        $this->services = [
            "backend.users.crud" => BackendUsersCrudService::class
        ];
    }

    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function install() {
        /**
         * @var $backendNavigation BackendNavigationService
         */
        $backendNavigation = Oforge()->Services()->get("backend.navigation");
        
        $backendNavigation->put([
            "name" => "admin",
            "order" => 100,
            "position" => "sidebar",
        ]);
    
        $backendNavigation->put([
            "name" => "user_management",
            "order" => 100,
            "parent" => "admin",
            "icon" => "fa fa-user",
            "path" => "backend_users",
            "position" => "sidebar",
        ]);

    }
}
