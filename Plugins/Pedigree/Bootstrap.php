<?php

namespace Pedigree;

use Insertion\Services\AttributeService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Pedigree\Controller\Backend\BackendPedigreeController;
use Pedigree\Models\Ancestor;
use Pedigree\Services\PedigreeService;

/**
 * Class Bootstrap
 *
 * @package Pedigree
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
        $this->endpoints = [
            BackendPedigreeController::class,
        ];

        $this->models = [
            Ancestor::class
        ];

        $this->services = [
            'pedigree' => PedigreeService::class
        ];

        $this->dependencies = [
          \Insertion\Bootstrap::class
        ];
    }

    public function install()
    {

    }

    public function activate()
    {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'backend_insertion_pedigree',
            'order'    => 5,
            'parent'   => 'backend_insertion',
            'icon'     => 'fa fa-pagelines',
            'path'     => 'backend_insertion_pedigree',
            'position' => 'sidebar',
        ]);
    }

    public function deactivate()
    {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->delete([
            'name'     => 'backend_insertion_pedigree',
        ]);
    }
}
