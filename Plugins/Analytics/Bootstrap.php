<?php

namespace Analytics;

use Analytics\Services\AnalyticsDataService;
use Oforge\Engine\Modules\AdminBackend\Core\Enums\DashboardWidgetPosition;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
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

    /** @inheritDoc */
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

        $configService->add([
            'name'     => 'analytics_tracking_id',
            'type'     => ConfigType::STRING,
            'group'    => 'analytics',
            'default'  => '',
            'label'    => 'config_analytics_tracking_id',
            'required' => true,
            'order'    => 0,
        ]);

        /**  @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->install([
            'name'     => 'plugin_analytics',
            'template' => 'Analytics',
            'handler'  => '',
            'label'    => [
                'en' => 'Google Analytics',
                'de' => 'Google Analytics',
            ],
            'position' => DashboardWidgetPosition::TOP,
            'cssClass' => '',
        ]);
    }

    /** @inheritDoc */
    public function uninstall() {
        /**  @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->uninstall('plugin_analytics');
    }

    /** @inheritDoc */
    public function activate() {
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_ADMIN);
        $backendNavigationService->add([
            'name'     => 'backend_analytics',
            'order'    => 6,
            'parent'   => BackendNavigationService::KEY_ADMIN,
            'icon'     => 'fa fa-bar-chart',
            'path'     => 'backend_analytics',
            'position' => 'sidebar',
        ]);
        /**  @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->activate('plugin_analytics');
    }

    /** @inheritDoc */
    public function deactivate() {
        /**  @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->deactivate('plugin_analytics');
    }

}
