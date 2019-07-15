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
        // I18N::translate('config_backend_project_footer_text', 'Copyright', 'en');
        // I18N::translate('config_backend_project_footer_text', 'Backend footer text', 'en');
        // I18N::translate('config_backend_sidebar_collapsed_default', 'Collapse sidebar default', 'en');
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'backend_project_footer_text',
            'type'     => ConfigType::STRING,
            'group'    => 'backend',
            'default'  => 'Oforge',
            'label'    => 'config_backend_project_footer_text',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'backend_sidebar_collapsed_default',
            'type'     => ConfigType::BOOLEAN,
            'group'    => 'backend',
            'default'  => false,
            'label'    => 'config_backend_sidebar_collapsed_default',
        ]);
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add([
            'name'     => 'backend_content',
            'order'    => 1,
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'admin',
            'order'    => 99,
            'position' => 'sidebar',
        ]);
    }
}
