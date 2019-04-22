<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 09:54
 */

namespace Oforge\Engine\Modules\UserManagement;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\UserManagement\Controller\Backend\ProfileController;
use Oforge\Engine\Modules\UserManagement\Controller\Backend\UserManagementController;
use Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\UserManagement
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            \Oforge\Engine\Modules\CRUD\Bootstrap::class,
            \Oforge\Engine\Modules\Auth\Bootstrap::class,
        ];

        $this->endpoints = [
            UserManagementController::class,
            ProfileController::class,
        ];

        $this->services = [
            'backend.users.crud' => BackendUsersCrudService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /** @var BackendNavigationService $backendNavigation */
        $backendNavigation = Oforge()->Services()->get('backend.navigation');

        $backendNavigation->put([
            'name'     => 'admin',
            'order'    => 100,
            'position' => 'sidebar',
        ]);
        $backendNavigation->put([
            'name'     => 'user_management',
            'order'    => 100,
            'parent'   => 'admin',
            'icon'     => 'fa fa-user',
            'path'     => 'backend_users',
            'position' => 'sidebar',
        ]);
    }

}
