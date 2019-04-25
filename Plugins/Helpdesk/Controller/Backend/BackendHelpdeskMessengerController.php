<?php

namespace Helpdesk\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Helpdesk\Services\HelpdeskMessengerService;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class BackendHelpdeskMessengerController extends SecureBackendController{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws OptimisticLockException
     */
    public function indexAction(Request $request, Response $response, $args) {
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

                $conversation = $helpdeskMessengerService->getConversationByTarget($targetId);

                $helpdeskMessengerService->sendMessage($conversation->getId(), 'helpdesk', $senderId, $message);

                $uri = $router->pathFor('backend_helpdesk_messenger');
                return $response->withRedirect($uri . '/' . $args['id'], 302);
            }

            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            $ticketData = $helpdeskTicketService->getTicketById($ticketId);

            /** @var HelpdeskMessengerService $helpdeskMessengerService */
            $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

            $conversation = $helpdeskMessengerService->getConversationByTarget($ticketId);

            $messages = $helpdeskMessengerService->getMessagesOfConversation($conversation->getId());
            if (!isset($messages[0])) {
                return $response->withRedirect($uri, 302);
            }

            Oforge()->View()->assign(['messages' => $messages]);
            Oforge()->View()->assign(['ticket' => $ticketData]);
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
