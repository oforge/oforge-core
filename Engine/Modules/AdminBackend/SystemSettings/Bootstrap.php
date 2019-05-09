<?php

namespace Oforge\Engine\Modules\AdminBackend\SystemSettings;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend\SystemSettingsController;
use Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend\SystemSettingsGroupController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\SystemSettings
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            SystemSettingsController::class,
            SystemSettingsGroupController::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     * @throws ParentNotFoundException
     */
    public function activate() {
        /** @var BackendNavigationService $backendNavigation */
        $backendNavigation = Oforge()->Services()->get('backend.navigation');
        $backendNavigation->put([
            'name'     => 'backend_settings',
            'order'    => 100,
            'parent'   => 'admin',
            'icon'     => 'fa fa-gears',
            'path'     => 'backend_settings',
            'position' => 'sidebar',
        ]);
        $backendNavigation->put([
            'name'     => 'backend_settings_group',
            'parent'   => 'backend_settings',
            'visible'  => false,
            'path'     => 'backend_settings_group',
            'position' => 'sidebar',
        ]);
    }

}
