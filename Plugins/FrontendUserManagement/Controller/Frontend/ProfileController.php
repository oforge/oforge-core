<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class ProfileController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {
        Oforge()->View()->assign(["msg" => "hello user"]);
    }
    public function editAction(Request $request, Response $response) {
    }
    public function deleteAction(Request $request, Response $response) {
    }
    
    /**
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("editAction", User::class);
        $this->ensurePermissions("deleteAction", User::class);
    }
}
