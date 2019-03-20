<?php

namespace Helpdesk;

use Helpdesk\Controller\Backend\BackendHelpdeskController;
use Helpdesk\Services\MessengerHelpdeskService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            '/backend/helpdesk[/]' => [
                'controller'   => BackendHelpdeskController::class,
                'name'         => 'backend_helpdesk',
                'assets_scope' => 'Backend',
            ],
        ];

        $this->services = [
            'helpdesk.messenger' => MessengerHelpdeskService::class,
        ];

    }

    public function activate() {
        /**
         * @var $sidebarNavigation BackendNavigationService
         */
        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name"     => "backend_helpdesk",
            "order"    => 4,
            "parent"   => "backend_content",
            "icon"     => "fa fa-support",
            "path"     => "backend_helpdesk",
            "position" => "sidebar",
        ]);
    }
}
