<?php

namespace Helpdesk;

use Exception;
use FrontendUserManagement\Services\AccountNavigationService;
use Helpdesk\Controller\Backend\BackendHelpdeskController;
use Helpdesk\Controller\Backend\BackendHelpdeskSettingsController;
use Helpdesk\Controller\Frontend\FrontendHelpdeskController;
use Helpdesk\Models\IssueTypeGroup;
use Helpdesk\Models\IssueTypes;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Helpdesk\Widgets\HelpdeskLastTicketsWidgetHandler;
use Helpdesk\Widgets\HelpdeskOpenTicketsWidgetHandler;
use Oforge\Engine\Modules\AdminBackend\Core\Enums\DashboardWidgetPosition;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

/**
 * Class Bootstrap
 *
 * @package Helpdesk
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            BackendHelpdeskController::class,
            BackendHelpdeskSettingsController::class,
            FrontendHelpdeskController::class,
        ];

        $this->services = [
            'helpdesk.messenger' => HelpdeskMessengerService::class,
            'helpdesk.ticket'    => HelpdeskTicketService::class,
        ];

        $this->models = [
            Ticket::class,
            IssueTypes::class,
            IssueTypeGroup::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
            \Messenger\Bootstrap::class,
        ];
    }

    /** @inheritDoc */
    public function install() {
        /**
         * @var GenericCrudService $crud
         * @var HelpdeskTicketService $helpdeskTicketService
         */
        $crud                  = Oforge()->Services()->get('crud');
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        try {
            /**
             * @var IssueTypeGroup $supportGroup
             * @var IssueTypeGroup $reportGroup
             */
            $supportGroup = $crud->create(IssueTypeGroup::class, ['issueTypeGroupName' => 'support']);
            $reportGroup  = $crud->create(IssueTypeGroup::class, ['issueTypeGroupName' => 'report']);
            $helpdeskTicketService->createIssueType('support_issue_type_1', $supportGroup);
            $helpdeskTicketService->createIssueType('support_issue_type_2', $supportGroup);
            $helpdeskTicketService->createIssueType('report_issue_type_1', $reportGroup);
            $helpdeskTicketService->createIssueType('report_issue_type_2', $reportGroup);
            $helpdeskTicketService->createNewTicket(1, 1, 'but the horse ain\'t one',
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');
            $helpdeskTicketService->createNewTicket(1, 1, 'but the horse ain\'t two',
                'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');
        } catch (ConfigElementAlreadyExistException $exception) {
            // ignore
        } catch (Exception $exception) {
            throw $exception;
        }

        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->install([
            'name'     => 'plugin_helpdesk_open_tickets',
            'template' => 'HelpdeskOpenTickets',
            'handler'  => HelpdeskOpenTicketsWidgetHandler::class,
            'label'    => [
                'en' => 'Open helpdesk tickets',
                'de' => 'Offene Helpdesk-Tickets',
            ],
            'position' => DashboardWidgetPosition::TOP,
            'cssClass' => 'bg-red',
        ]);
        $dashboardWidgetsService->install([
            'name'     => 'plugin_helpdesk_last_tickets',
            'template' => 'HelpdeskLastTickets',
            'handler'  => HelpdeskLastTicketsWidgetHandler::class,
            'label'    => [
                'en' => 'Last helpdesk tickets',
                'de' => 'Letzte Helpdesk-Tickets',
            ],
            'position' => DashboardWidgetPosition::LEFT,
            'cssClass' => 'box-success',
        ]);
    }

    /** @inheritDoc */
    public function uninstall() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->uninstall("plugin_helpdesk_last_tickets");
        $dashboardWidgetsService->uninstall("plugin_helpdesk_open_tickets");
    }

    /** @inheritDoc */
    public function activate() {
        /**
         * @var AccountNavigationService $accountNavigation
         * @var BackendNavigationService $backendNavigationService
         */
        $accountNavigation        = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        $backendNavigationService->add([
            'name'     => 'backend_helpdesk',
            'order'    => 4,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-support',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_helpdesk_tickets',
            'order'    => 1,
            'parent'   => 'backend_helpdesk',
            'icon'     => 'fa fa-ticket',
            'path'     => 'backend_helpdesk',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_helpdesk_settings',
            'order'    => 1,
            'parent'   => 'backend_helpdesk',
            'icon'     => 'fa fa-gear',
            'path'     => 'backend_helpdesk_settings',
            'position' => 'sidebar',
        ]);
        $accountNavigation->put([
            'name'     => 'frontend_account_support',
            'order'    => 1,
            'icon'     => 'support',
            'path'     => 'frontend_account_support',
            'position' => 'sidebar',
        ]);
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->activate("plugin_helpdesk_last_tickets");
        $dashboardWidgetsService->activate("plugin_helpdesk_open_tickets");
    }

    /** @inheritDoc */
    public function deactivate() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->deactivate("plugin_helpdesk_last_tickets");
        $dashboardWidgetsService->deactivate("plugin_helpdesk_open_tickets");
    }

}
