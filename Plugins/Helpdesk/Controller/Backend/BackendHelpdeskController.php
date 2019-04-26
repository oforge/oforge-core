<?php

namespace Helpdesk\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class BackendHelpdeskController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        /** @var Ticket[] $ticketData */
        $ticketData = $helpdeskTicketService->getTickets();
        $tickets = [];

        /** @var UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');

        foreach ($ticketData as $ticket) {
            /** @var User $user */
            $user = $userService->getUserById($ticket->getOpener());
            $ticket = $ticket->toArray();
            $ticket['email'] = $user->getEmail();
            array_push($tickets, $ticket);
        }

        Oforge()->View()->assign([ "content" => ["ticketData" => $tickets]]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function closeTicketAction(Request $request, Response $response) {
        if($request->isPost()) {
            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            $ticketId = $request->getParsedBody()['ticketId'];

            $helpdeskTicketService->changeStatus($ticketId, "closed");

            return $response->withRedirect('/backend/helpdesk');
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}