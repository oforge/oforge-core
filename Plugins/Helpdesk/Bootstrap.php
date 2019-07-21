<?php

namespace Helpdesk;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use Helpdesk\Controller\Backend\BackendHelpdeskController;
use Helpdesk\Controller\Backend\BackendHelpdeskSettingsController;
use Helpdesk\Controller\Frontend\FrontendHelpdeskController;
use Helpdesk\Models\IssueTypeGroup;
use Helpdesk\Models\IssueTypes;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Helpdesk\Widgets\HelpdeskCountWidgetHandler;
use Helpdesk\Widgets\HelpdeskWidgetHandler;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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

    /**
     * @throws ConfigElementAlreadyExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws \ReflectionException
     */
    public function install() {
        /** @var GenericCrudService $crud */
        $crud = Oforge()->Services()->get('crud');
        /** @var IssueTypeGroup $supportGroup */
        $supportGroup = $crud->create(IssueTypeGroup::class, ['issueTypeGroupName' => 'support']);
        /** @var IssueTypeGroup $reportGroup */
        $reportGroup = $crud->create(IssueTypeGroup::class, ['issueTypeGroupName' => 'report']);

        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        $helpdeskTicketService->createIssueType('support_issue_type_1', $supportGroup);
        $helpdeskTicketService->createIssueType('support_issue_type_2', $supportGroup);
        $helpdeskTicketService->createIssueType('report_issue_type_1', $reportGroup);
        $helpdeskTicketService->createIssueType('report_issue_type_2', $reportGroup);

        $helpdeskTicketService->createNewTicket(1, 1, 'but the horse ain\'t one',
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');

        $helpdeskTicketService->createNewTicket(1, 1, 'but the horse ain\'t two',
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');
    }

    /**
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function activate() {
        /**
         * @var AccountNavigationService $accountNavigation
         * @var DashboardWidgetsService $dashboardWidgetsService
         * @var BackendNavigationService $backendNavigationService
         */
        $accountNavigation        = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $dashboardWidgetsService  = Oforge()->Services()->get('backend.dashboard.widgets');
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
        $dashboardWidgetsService->register([
            'position'     => 'left',
            'action'       => HelpdeskWidgetHandler::class,
            'title'        => 'frontend_widget_helpdesk_title',
            'name'         => 'frontend_widget_helpdesk',
            'cssClass'     => 'box-success',
            'templateName' => 'Helpdesk',
        ]);

        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => HelpdeskCountWidgetHandler::class,
            'title'        => 'frontend_widget_helpdesk_count_title',
            'name'         => 'frontend_widget_helpdesk_count',
            'cssClass'     => 'bg-red',
            'templateName' => 'HelpdeskCount',
        ]);

    }

    public function load() {
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');

        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => HelpdeskCountWidgetHandler::class,
            'title'        => 'frontend_widget_helpdesk_count_title',
            'name'         => 'frontend_widget_helpdesk_count',
            'cssClass'     => 'bg-red',
            'templateName' => 'HelpdeskCount',
        ]);
    }

}
