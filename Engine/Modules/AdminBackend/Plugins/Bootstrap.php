<?php

namespace Oforge\Engine\Modules\AdminBackend\Plugins;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend\PluginController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\Plugins
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            PluginController::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     */
    public function install() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add([
            'name'     => 'backend_plugins',
            'order'    => 1,
            'parent'   => BackendNavigationService::KEY_ADMIN,
            'icon'     => 'fa fa-puzzle-piece',
            'path'     => 'backend_plugins',
            'position' => 'sidebar',
        ]);
    }

}
