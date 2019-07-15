<?php

namespace Oforge\Engine\Modules\AdminBackend\Cronjob;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Cronjob\Controller\Backend\CronjobController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            CronjobController::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     */
    public function install() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_ADMIN);
        $backendNavigationService->add([
            'name'     => 'backend_cronjob',
            'order'    => 6,
            'parent'   => BackendNavigationService::KEY_ADMIN,
            'icon'     => 'fa fa-history',
            'path'     => 'backend_cronjob',
            'position' => 'sidebar',
        ]);
    }

}
