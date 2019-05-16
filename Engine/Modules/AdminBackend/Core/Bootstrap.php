<?php

namespace Oforge\Engine\Modules\AdminBackend\Core;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\DashboardController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\FavoritesController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\IndexController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\LoginController;
use Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend\LogoutController;
use Oforge\Engine\Modules\AdminBackend\Core\Middleware\BackendSecureMiddleware;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendUserFavorites;
use Oforge\Engine\Modules\AdminBackend\Core\Models\DashboardWidget;
use Oforge\Engine\Modules\AdminBackend\Core\Models\UserDashboardWidgets;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\UserFavoritesService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            IndexController::class,
            LoginController::class,
            LogoutController::class,
            DashboardController::class,
            FavoritesController::class,
        ];

        $this->middlewares = [
            'backend' => ['class' => BackendSecureMiddleware::class, 'position' => 1],
        ];

        $this->models = [
            BackendNavigation::class,
            BackendUserFavorites::class,
            DashboardWidget::class,
            UserDashboardWidgets::class,
        ];

        $this->services = [
            'backend.navigation'        => BackendNavigationService::class,
            "backend.dashboard.widgets" => DashboardWidgetsService::class,
            'backend.favorites'         => UserFavoritesService::class,
        ];

        $this->order = 3;
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
        //TODO in import csv
        // I18N::translate('config_system_project_name', 'Project name', 'en');
        // I18N::translate('config_system_project_short', 'Project short name', 'en');
        // I18N::translate('config_backend_project_footer_text', 'Copyright', 'en');
        // I18N::translate('config_backend_project_footer_text', 'Backend footer text', 'en');
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'system_project_name',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'Oforge',
            'label'    => 'config_system_project_name',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_short',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'OF',
            'label'    => 'config_system_project_short',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'system_project_copyright',
            'type'     => ConfigType::STRING,
            'group'    => 'system',
            'default'  => 'Oforge',
            'label'    => 'config_system_project_copyright',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'backend_project_footer_text',
            'type'     => ConfigType::STRING,
            'group'    => 'backend',
            'default'  => 'Oforge',
            'label'    => 'config_backend_project_footer_text',
            'required' => true,
        ]);

        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'content',
            'order'    => 1,
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'admin',
            'order'    => 99,
            'position' => 'sidebar',
        ]);
        /*
        $sidebarNavigation->put([
            'name' => 'help',
            'order' => 99,
            'parent' => 'admin',
            'icon' => 'ion-help'
        ]);
        $sidebarNavigation->put([
            'name' => 'ionicons',
            'order' => 2,
            'parent' => 'help',
            'icon' => 'ion-nuclear',
            'path' => 'backend_dashboard_ionicons'
        ]);
        $sidebarNavigation->put([
            'name' => 'fontAwesome',
            'order' => 1,
            'parent' => 'help',
            'icon' => 'fa fa-fort-awesome',
            'path' => 'backend_dashboard_fontAwesome'
        ]);
        */
    }

}
