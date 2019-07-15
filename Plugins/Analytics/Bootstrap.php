<?php

namespace Analytics;

use Analytics\Services\AnalyticsDataService;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Analytics
 */
class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints   = [
            Controller\Backend\AnalyticsController::class,
        ];
        $this->middlewares = [];
        $this->models      = [];
        $this->services    = [
            'analytics.data' => AnalyticsDataService::class,
        ];
    }

    /**
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function install() {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'     => 'analytics_api_key',
            'type'     => ConfigType::STRING,
            'group'    => 'analytics',
            'default'  => 0,
            'label'    => 'config_analytics_api_key',
            'required' => true,
            'order'    => 0,
        ]);
    }

    /**
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException
     */
    public function activate() {
        /**
         * @var DashboardWidgetsService $dashboardWidgetsService
         * @var BackendNavigationService $backendNavigationService
         */
        $dashboardWidgetsService  = Oforge()->Services()->get('backend.dashboard.widgets');
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => '',
            'title'        => '',
            'name'         => 'analytics',
            'cssClass'     => '',
            'templateName' => 'Analytics',
        ]);

        $backendNavigationService->add(BackendNavigationService::CONFIG_ADMIN);
        $backendNavigationService->add([
            'name'     => 'backend_analytics',
            'order'    => 6,
            'parent'   => BackendNavigationService::KEY_ADMIN,
            'icon'     => 'fa fa-bar-chart',
            'path'     => 'backend_analytics',
            'position' => 'sidebar',
        ]);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function deactivate() {
        /** @var  $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->unregister('analytics');
    }
}
