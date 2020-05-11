<?php

namespace TestPlugin;

use TestPlugin\Models\TestModel;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use TestPlugin\Controller\Backend\BackendTestController;
use TestPlugin\Controller\Frontend\FrontendTestController;
use TestPlugin\Services\TestService;

/**
 * Class Bootstrap
 *
 * @package TestPlugin
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
        $this->endpoints = [
            BackendTestController::class,
            FrontendTestController::class
        ];

        $this->services = [
            'testplugin.testservice' => TestService::class
        ];

        $this->models = [
            TestModel::class
        ];
    }

    /** @inheritDoc */
    public function install()
    {
    }

    public function activate()
    {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'backend_test',
            'order'    => 12,
            'parent'   => 'backend_admin',
            'icon'     => 'fa fa-pagelines',
            'path'     => 'backend_test',
            'position' => 'sidebar',
        ]);
    }

    public function deactivate()
    {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->delete([
            'name'     => 'backend_test',
        ]);
    }
}
