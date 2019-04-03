<?php

namespace Oforge\Engine\Modules\CRUD;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\CRUD\Controller\Backend\CRUD\Test\ReadController;
use Oforge\Engine\Modules\CRUD\Controller\Backend\CRUD\Test\WriteController;
use Oforge\Engine\Modules\CRUD\Models\CrudTest;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\CRUD
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            '/backend/crudtest/read'  => ['controller' => ReadController::class, 'name' => 'backend_crudtest_read', 'asset_scope' => 'Backend'],
            '/backend/crudtest/write' => ['controller' => WriteController::class, 'name' => 'backend_crudtest_write', 'asset_scope' => 'Backend'],
        ];
        $this->services  = [
            'crud' => GenericCrudService::class,
        ];
        $this->models = [
            CrudTest::class,
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function install() {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'backend_crudtest',
            'order'    => 100,
            'parent'   => 'admin',
            'icon'     => 'glyphicon glyphicon glyphicon-th',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_crudtest_read',
            'order'    => 1,
            'parent'   => 'backend_crudtest',
            'icon'     => 'fa fa-search',
            'path'     => 'backend_crudtest_read',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_crudtest_write',
            'order'    => 2,
            'parent'   => 'backend_crudtest',
            'icon'     => 'fa fa-pencil',
            'path'     => 'backend_crudtest_write',
            'position' => 'sidebar',
        ]);
    }

}
