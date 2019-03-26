<?php

namespace Helpdesk\Controller\Backend;

use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Slim\Http\Request;
use Slim\Http\Response;

class BackendHelpdeskController extends SecureBackendController {

    public function indexAction(Request $request, Response $response) {
        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

        $ticketData = $helpdeskTicketService->getTickets();
        Oforge()->View()->assign(["tickets" => $ticketData]);
    }

    public function closeTicketAction(Request $request, Response $response) {
        if($request->isPost()) {
            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            $ticketId = $request->getParsedBody()['ticketId'];

            $helpdeskTicketService->changeStatus($ticketId, "closed");

            return $response->withRedirect('/backend/helpdesk');
        }
    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}