<?php
namespace Helpdesk\Controller\Frontend;

use Slim\Http\Request;
use Slim\Http\Response;

class FrontendHelpdeskTicketController {
    public function indexAction(Request $request, Response $response, $args) {

        if ($request->isPost()) {

        }
        /* When there is a ticket id, redirect to the MessageController */
        if (isset($args) && isset($args['id'])) {

        }
    }
}
