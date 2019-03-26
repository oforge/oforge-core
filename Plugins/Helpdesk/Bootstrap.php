<?php

namespace Helpdesk;

use Helpdesk\Controller\Backend\BackendHelpdeskController;
use Helpdesk\Controller\Backend\BackendHelpdeskMessengerController;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            '/backend/helpdesk'                => [
                'controller'  => BackendHelpdeskController::class,
                'name'        => 'backend_helpdesk',
                'asset_scope' => 'Backend',
            ],
            '/backend/helpdesk/messenger/{id}' => [
                'controller'  => BackendHelpdeskMessengerController::class,
                'name'        => 'backend_helpdesk_messenger',
                'asset_scope' => 'Backend',
            ],
        ];

        $this->services = [
            'helpdesk.messenger' => HelpdeskMessengerService::class,
            'helpdesk.ticket'    => HelpdeskTicketService::class,
        ];

        $this->models = [
            Ticket::class,
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
