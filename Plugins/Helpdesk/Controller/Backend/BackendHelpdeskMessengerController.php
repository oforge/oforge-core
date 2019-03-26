<?php

namespace Helpdesk\Controller\Backend;

use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Slim\Http\Request;
use Slim\Http\Response;

class BackendHelpdeskMessengerController extends SecureBackendController{
    /**
     * @param Request $reqeust
     * @param Response $response
     * @param $args
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response, $args) {
        $ticketId = $args['id'];

        if($request->isPost()) {
            $targetId = $args['id'];

            $senderId = $request->getParsedBody()['sender'];
            $message = $request->getParsedBody()['message'];

            /** @var HelpdeskMessengerService $helpdeskMessengerService */
            $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

            $conversation = $helpdeskMessengerService->getConversationByTarget($targetId);

            $helpdeskMessengerService->sendMessage($conversation->getId(), 'helpdesk', $senderId, $message);

            return $response->withRedirect('/backend/helpdesk/messenger/' . $args['id']);
        }


        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

        $ticketData = $helpdeskTicketService->getTicketById($ticketId);


        /** @var HelpdeskMessengerService $helpdeskMessengerService */
        $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

        $conversation = $helpdeskMessengerService->getConversationByTarget($ticketId);

        $messages = $helpdeskMessengerService->getMessagesOfConversation($conversation->getId());
        if(!isset($messages[0])) {
            return $response->withRedirect('/backend/helpdesk');
        }

        Oforge()->View()->assign(['messages' => $messages]);
        Oforge()->View()->assign(['ticket' => $ticketData]);
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}