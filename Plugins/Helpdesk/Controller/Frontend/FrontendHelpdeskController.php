<?php

namespace Helpdesk\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class FrontendHelpdeskController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {
        // TODO
    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("processAction", User::class);
    }
}