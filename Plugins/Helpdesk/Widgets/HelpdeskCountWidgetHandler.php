<?php
/**
 * Created by PhpStorm.
 * User: motte
 * Date: 24.04.2019
 * Time: 16:08
 */

namespace Helpdesk\Widgets;

use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Widgets\DashboardWidgetInterface;

class HelpdeskCountWidgetHandler implements DashboardWidgetInterface {


    function getData() : array {

        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

        $ticketData = $helpdeskTicketService->count();

        return ["count" => $ticketData];
    }
}