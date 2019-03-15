<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class UserDetailsController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {

    }
    public function process_detailsAction(Request $request, Response $response) {

    }
    public function process_addressAction(Request $request, Response $response) {

    }

    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("processDetailsAction", User::class);
        $this->ensurePermissions("processAddressAction", User::class);
    }
}