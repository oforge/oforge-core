<?php

namespace Oforge\Engine\Modules\CRUD;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
// use Oforge\Engine\Modules\CRUD\Controller\Backend\CRUD\Test\ReadController;
// use Oforge\Engine\Modules\CRUD\Controller\Backend\CRUD\Test\WriteController;
// use Oforge\Engine\Modules\CRUD\Models\CrudTest;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\CRUD
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            // '/backend/crudtest/read'  => ReadController::getBootstrapEndpointsArray(),
            // '/backend/crudtest/write' => WriteController::getBootstrapEndpointsArray(),
        ];
        $this->services  = [
            'crud' => GenericCrudService::class,
        ];
        $this->models    = [
            // CrudTest::class,
        ];
    }

    /**
     */
    public function install() {
        /** @var BackendNavigationService $sidebarNavigation */ // $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        // $sidebarNavigation->put([
        //     'name'     => 'backend_crudtest',
        //     'order'    => 100,
        //     'parent'   => 'admin',
        //     'icon'     => 'glyphicon glyphicon glyphicon-th',
        //     'position' => 'sidebar',
        // ]);
        // $sidebarNavigation->put([
        //     'name'     => 'backend_crudtest_read',
        //     'order'    => 1,
        //     'parent'   => 'backend_crudtest',
        //     'icon'     => 'fa fa-search',
        //     'path'     => 'backend_crudtest_read',
        //     'position' => 'sidebar',
        // ]);
        // $sidebarNavigation->put([
        //     'name'     => 'backend_crudtest_write',
        //     'order'    => 2,
        //     'parent'   => 'backend_crudtest',
        //     'icon'     => 'fa fa-pencil',
        //     'path'     => 'backend_crudtest_write',
        //     'position' => 'sidebar',
        // ]);
    }

}
