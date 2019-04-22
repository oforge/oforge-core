<?php

namespace Helpdesk\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FrontendHelpdeskTicketController
 *
 * @package Helpdesk\Controller\Frontend
 * @EndpointClass(path="/account/support/ticket", name="frontend_account_support_ticket", assetScope="Frontend")
 */
class FrontendHelpdeskTicketController {

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response, $args) {
        if ($request->isPost()) {

        }
        /* When there is a ticket id, redirect to the MessageController */
        if (isset($args) && isset($args['id'])) {

        }
    }

}
