<?php

namespace Messenger\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Messenger\Models\Conversation;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class MessengerController extends SecureFrontendController {
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function indexAction(Request $request, Response $response, $args) {
        /** @var FrontendMessengerService $frontendMessengerService */
        $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
        $user = Oforge()->View()->get('user');

        /** @var Conversation[] $conversationList */
        $conversationList = $frontendMessengerService->getConversationList($user['id']);
        Oforge()->View()->assign(['conversationList' => $conversationList]);

        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            /* Create a new message for a given conversation */
            if ($request->isPost()) {
                return $response;
            }

            $conversation = $frontendMessengerService->getConversationById($args['id']);
            Oforge()->View()->assign(['conversation' => $conversation]);
        } else {
            if (sizeof($conversationList) > 0) {
                /** @var Router $router */
                $router = Oforge()->App()->getContainer()->get('router');
                $uri = $router->pathFor('frontend_account_messages') . '/' . $conversationList[0]->getId();
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
