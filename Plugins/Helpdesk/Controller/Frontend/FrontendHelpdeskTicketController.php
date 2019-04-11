<?php
namespace Helpdesk\Controller\Frontend;

use Slim\Http\Request;
use Slim\Http\Response;

class FrontendHelpdeskTicketController {
    public function indexAction(Request $request, Response $response, $args) {
        Oforge()->View()->assign(['message' => 'Go to Account -> Messages?']);
        print_r($args['id']);
    }
}
