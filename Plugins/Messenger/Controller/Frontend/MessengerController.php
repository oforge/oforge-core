<?php

namespace Messenger\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class MessengerController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response, $args) {

        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            /* Create a new message for a given conversation */
            if ($request->isPost()) {
                return $response;
            }

            $messengerFrontendService = Oforge()->Services()->get('frontend.messenger');

        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
    }
}
