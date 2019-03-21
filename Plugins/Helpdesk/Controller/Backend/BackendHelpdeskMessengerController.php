<?php

namespace Helpdesk\Controller\Backend;

use Helpdesk\Services\HelpdeskMessengerService;
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
    public function indexAction(Request $reqeust, Response $response, $args) {
        $ticketId = $args['id'];
        /** @var HelpdeskMessengerService $helpdeskMessengerService */
        $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

        $messages = $helpdeskMessengerService->getConversationByTarget($ticketId);

        Oforge()->View()->assign(['messages' => $messages]);
    }

    /**
     * @param Request $reqeust
     * @param Response $response
     * @param $args
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Exception
     */
    public function sendAction(Request $request, Response $response, $args) {
        if($request->isPost()) {
            $targetId = $args['id'];

            $message = $request->getParsedBody()['message'];

            /** @var HelpdeskMessengerService $helpdeskMessengerService */
            $helpdeskMessengerService = Oforge()->Services()->get('helpdesk.messenger');

            $conversation = $helpdeskMessengerService->getConversationByTarget($targetId);

            $helpdeskMessengerService->sendMessage($conversation->getId(), 'helpdesk?', $message);

            $response->withRedirect('/backend/helpdesk/messenger/' . $args['id']);
        }
        $response->withRedirect('/');
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}