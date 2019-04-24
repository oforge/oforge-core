<?php

namespace Messenger\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class MessengerController extends SecureFrontendController {
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function indexAction(Request $request, Response $response, $args) {

        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            /* Create a new message for a given conversation */
            if ($request->isPost()) {
                return $response;
            }

            /** @var FrontendMessengerService $frontendMessengerService */
            $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
            $conversation = $frontendMessengerService->getConversationById($args['id']);
            Oforge()->View()->assign(['conversation' => $conversation]);
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
    }
}
