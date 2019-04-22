<?php

namespace Messenger\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

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
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction(path="[/{id:.*}]", name="messages")
     */
    public function indexAction(Request $request, Response $response, $args) {
        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            /* Create a new message for a given conversation */
            if ($request->isPost()) {
                return $response;
            }

            $messengerFrontendService = Oforge()->Services()->get('messenger');
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
    }

}
