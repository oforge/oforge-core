<?php
/**
 * Created by PhpStorm.
 * User: motte
 * Date: 24.04.2019
 * Time: 16:08
 */

namespace Helpdesk\Widgets;

use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\DashboardWidgetInterface;

/**
 * Class HelpdeskLastTicketsWidgetHandler
 *
 * @package Helpdesk\Widgets
 */
class HelpdeskLastTicketsWidgetHandler implements DashboardWidgetInterface {

    /** @inheritDoc */
    public function prepareData() : array {
        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        /** @var UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');
        /** @var Ticket[] $tickets */
        $tickets = $helpdeskTicketService->getTickets();
        $tickets = array_slice($tickets, 0, 10);
        foreach ($tickets as $index => $ticket) {
            /** @var User $user */
            $user            = $userService->getUserById($ticket->getOpener());
            $data            = $ticket->toArray();
            $data['email']   = isset($user) ? $user->getEmail() : $ticket->getOpener();
            $tickets[$index] = $data;
        }

        return ['ticketData' => $tickets];
    }

}
