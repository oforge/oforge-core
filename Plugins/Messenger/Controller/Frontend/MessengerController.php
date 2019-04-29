<?php

namespace Messenger\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Helpdesk\Services\HelpdeskMessengerService;
use Messenger\Models\Conversation;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class MessengerController
 *
 * @package Messenger\Controller\Frontend
 * @EndpointClass(path="/account/messages", name="frontend_account", assetScope="Frontend")
 */
class MessengerController extends SecureFrontendController {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction(path="[/{id:.*}]", name="messages")
     */
    public function indexAction(Request $request, Response $response, array $args) {
        /** @var FrontendMessengerService $frontendMessengerService */
        $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
        $user = Oforge()->View()->get('user');

        /** @var Conversation[] $conversationList */
        $conversationList = $frontendMessengerService->getConversationList($user['id']);
        Oforge()->View()->assign(['conversationList' => $conversationList]);

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            /* Create a new message for a given conversation */

            if ($request->isPost()) {
                $conversationId = $args['id'];

                $senderId = $request->getParsedBody()['sender'];
                $message  = $request->getParsedBody()['message'];

                /** @var FrontendMessengerService $frontendMessengerService */
                $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');

                $conversation = $frontendMessengerService->getConversation($conversationId, $senderId);

                $frontendMessengerService->sendMessage($conversation['id'], 'frontend', $senderId, $message);

                $uri = $router->pathFor('frontend_account_messages');

                return $response->withRedirect($uri . '/' . $conversation['id'], 302);
            }

            $conversation = $frontendMessengerService->getConversation($args['id'], $user['id']);
            Oforge()->View()->assign(['conversation' => $conversation]);
        } else {
            if (sizeof($conversationList) > 0) {
                /** @var Router $router */
                $router = Oforge()->App()->getContainer()->get('router');
                $uri = $router->pathFor('frontend_account_messages') . '/' . $conversationList[0]['id'];
                return $response->withRedirect($uri, 302);
            }
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
    }
}
