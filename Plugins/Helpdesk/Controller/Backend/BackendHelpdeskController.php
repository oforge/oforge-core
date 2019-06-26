<?php

namespace Helpdesk\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Helpdesk\Models\Ticket;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BackendHelpdeskController
 *
 * @package Helpdesk\Controller\Backend
 * @EndpointClass(path="/backend/helpdesk", name="backend_helpdesk", assetScope="Backend")
 */
class BackendHelpdeskController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction()
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
            $tickets[] = $ticket;
        }

        Oforge()->View()->assign([ "content" => ["ticketData" => $tickets]]);
    }

    /**
     * @param Request $request
     * @param Response $response*
     * @EndpointAction(path="/closeTicket")
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     *
     */
    public function closeTicketAction(Request $request, Response $response) {
        if ($request->isPost()) {
            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            $ticketId = $request->getParsedBody()['ticketId'];

            $helpdeskTicketService->changeStatus($ticketId, 'closed');

            return $response->withRedirect('/backend/helpdesk');
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/messenger/{id}")
     */
    public function messengerAction(Request $request, Response $response, array $args) {
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri = $router->pathFor('backend_helpdesk');

        if (isset($args) && isset($args['id'])) {
            $ticketId = $args['id'];

            if ($request->isPost()) {
                $targetId = $args['id'];

                $senderId = $request->getParsedBody()['sender'];
                $message  = $request->getParsedBody()['message'];

                if (!$senderId) {
                    $senderId = 'helpdesk';
                }

                /** @var HelpdeskMessengerService $helpdeskMessengerService */
                $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

                $conversation = $helpdeskMessengerService->getConversationsByTarget($targetId, $senderId);

                if (sizeof($conversation) > 0) {
                    $conversation = $conversation[0];
                }

                $helpdeskMessengerService->sendMessage($conversation['id'], 'helpdesk', $senderId, $message);

                $uri = $router->pathFor('backend_helpdesk_messenger', ['id' => $args['id']]);
                return $response->withRedirect($uri , 302);
            }

            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            $ticketData = $helpdeskTicketService->getTicketById($ticketId);

            /** @var HelpdeskMessengerService $helpdeskMessengerService */
            $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

            $conversation = $helpdeskMessengerService->getConversationsByTarget($ticketId, 'helpdesk');

            if (sizeof($conversation) > 0) {
                $conversation = $conversation[0];
            }

            $messages = $helpdeskMessengerService->getMessagesOfConversation($conversation['id']);
            if (!isset($messages[0])) {
                return $response->withRedirect($uri, 302);
            }

            Oforge()->View()->assign([
                'messages' => $messages,
                'ticket'   => $ticketData,
            ]);
        } else {
            return $response->withRedirect($uri, 302);
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
