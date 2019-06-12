<?php

namespace Oforge\Engine\Modules\AdminBackend\Documentation;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Documentation\Controller\Backend\DocumentationUIController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\Documentation
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->dependencies = [
            \Oforge\Engine\Modules\AdminBackend\Core\Bootstrap::class,
        ];

        /*
        $this->endpoints = [
            DocumentationUIController::class,
        ];
        */
    }

    /**
     *
     */
    public function install() {
        /** @var BackendNavigationService $sidebarNavigation */
        /*
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'backend_documentation',
            'order'    => 100,
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_ui_elements',
            'order'    => 100,
            'parent'   => 'backend_documentation',
            'icon'     => 'fa fa-laptop',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_documentation_ui_general',
            'order'    => 1,
            'parent'   => 'backend_ui_elements',
            'icon'     => 'fa fa-circle-o',
            'path'     => 'backend_documentation_ui_general',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_documentation_ui_icons',
            'order'    => 2,
            'parent'   => 'backend_ui_elements',
            'icon'     => 'fa fa-circle-o',
            'path'     => 'backend_documentation_ui_icons',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_documentation_ui_buttons',
            'order'    => 3,
            'parent'   => 'backend_ui_elements',
            'icon'     => 'fa fa-circle-o',
            'path'     => 'backend_documentation_ui_buttons',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_documentation_ui_sliders',
            'order'    => 4,
            'parent'   => 'backend_ui_elements',
            'icon'     => 'fa fa-circle-o',
            'path'     => 'backend_documentation_ui_sliders',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_documentation_ui_timeline',
            'order'    => 5,
            'parent'   => 'backend_ui_elements',
            'icon'     => 'fa fa-circle-o',
            'path'     => 'backend_documentation_ui_timeline',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_documentation_ui_modals',
            'order'    => 6,
            'parent'   => 'backend_ui_elements',
            'icon'     => 'fa fa-circle-o',
            'path'     => 'backend_documentation_ui_modals',
            'position' => 'sidebar',
        ]); */
    }

}
