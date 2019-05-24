<?php
namespace Analytics;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Analytics\Controller\Backend\AnalyticsController;
/**
 * Class Bootstrap
 *
 * @package Analytics
 */
class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            Controller\Backend\AnalyticsController::class,
        ];
        $this->middlewares = [
        ];
        $this->models = [
        ];
        $this->services = [
        ];
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

        /** @var DashboardWidgetsService $dashboardWidgetsService
         *  @var BackendNavigationService $sidebarNavigation
         */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $sidebarNavigation       = Oforge()->Services()->get('backend.navigation');

        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => '',
            'title'        => '',
            'name'         => 'analytics',
            'cssClass'     => '',
            'templateName' => 'Analytics',
        ]);

        $sidebarNavigation->put([
            'name'     => 'backend_analytics',
            'order'    => 6,
            'parent'   => 'backend_admin',
            'icon'     => 'fa fa-bar-chart',
            'path'     => 'analytics',
            'position' => 'sidebar',
        ]);
    }
    public function deactivate() {
    }
}