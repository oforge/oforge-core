<?php

namespace Helpdesk\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Slim\Http\Request;
use Slim\Http\Response;

class BackendHelpdeskController extends SecureBackendController {

    public function indexAction(Request $request, Response $response) {

    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}