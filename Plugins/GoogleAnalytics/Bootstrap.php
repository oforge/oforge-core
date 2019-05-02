<?php

namespace GoogleAnalytics;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class Bootstrap
 *
 * @package GoogleAnalytics
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
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
     * @throws ConfigOptionKeyNotExistsException
     * @throws ServiceNotFoundException
     */
    public function activate() {

        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => '',
            'title'        => '',
            'name'         => 'google_analytics',
            'cssClass'     => '',
            'templateName' => 'GoogleAnalytics',
        ]);
    }

    public function deactivate() {
    }

}
