<?php

namespace Helpdesk;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use Helpdesk\Controller\Backend\BackendHelpdeskController;
use Helpdesk\Controller\Backend\BackendHelpdeskMessengerController;
use Helpdesk\Controller\Frontend\FrontendHelpdeskController;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
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
        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
        ];

        $this->endpoints = [
            BackendHelpdeskController::class,
            BackendHelpdeskMessengerController::class,
            FrontendHelpdeskController::class,
        ];

        $this->models = [
            Ticket::class,
        ];

        $this->services = [
            'helpdesk.messenger' => HelpdeskMessengerService::class,
            'helpdesk.ticket'    => HelpdeskTicketService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function activate() {
        /** @var BackendNavigationService $sidebarNavigation */
        /** @var AccountNavigationService $accountNavigation */
        $accountNavigation = Oforge()->Services()->get('frontend.user.management.account.navigation');
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');

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
    }

}
