<?php

namespace Helpdesk;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use Helpdesk\Controller\Backend\BackendHelpdeskController;
use Helpdesk\Controller\Backend\BackendHelpdeskMessengerController;
use Helpdesk\Controller\Frontend\FrontendHelpdeskController;
use Helpdesk\Models\IssueTypes;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Helpdesk\Widgets\HelpdeskWidgetHandler;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

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
            BackendHelpdeskMessengerController::class,
            FrontendHelpdeskController::class,
        ];

        $this->services = [
            'helpdesk.messenger' => HelpdeskMessengerService::class,
            'helpdesk.ticket'    => HelpdeskTicketService::class,
        ];

        $this->models = [
            Ticket::class,
            IssueTypes::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
            \Messenger\Bootstrap::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        $helpdeskTicketService->createIssueType('99 Problems');
        $helpdeskTicketService->createIssueType('but the horse ain\'t one');

        $helpdeskTicketService->createNewTicket(1, 1, 'but the horse ain\'t one',
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');

        $helpdeskTicketService->createNewTicket(1, 1, 'but the horse ain\'t two',
            'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.');
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function activate() {
        /**
         * @var AccountNavigationService $accountNavigation
         * @var DashboardWidgetsService $dashboardWidgetsService
         * @var BackendNavigationService $sidebarNavigation
         */
        $accountNavigation       = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $sidebarNavigation       = Oforge()->Services()->get('backend.navigation');

        $sidebarNavigation->put([
            'name'     => 'backend_helpdesk',
            'order'    => 4,
            'parent'   => 'backend_content',
            'icon'     => 'fa fa-support',
            'path'     => 'backend_helpdesk',
            'position' => 'sidebar',
        ]);
        $accountNavigation->put([
            'name'     => 'frontend_account_support',
            'order'    => 1,
            'icon'     => 'whatsapp',
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
    }

}
