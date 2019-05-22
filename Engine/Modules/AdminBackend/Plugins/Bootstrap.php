<?php

namespace Oforge\Engine\Modules\AdminBackend\Plugins;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Plugins\Controller\Backend\PluginController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
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
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ParentNotFoundException
     */
    public function install() {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'backend_plugins',
            'order'    => 1,
            'parent'   => 'admin',
            'icon'     => 'fa fa-puzzle-piece',
            'path'     => 'backend_plugins',
            'position' => 'sidebar',
        ]);
    }

}
