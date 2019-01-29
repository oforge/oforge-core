<?php
namespace Oforge\Engine\Modules\AdminBackendPlugin\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Slim\Http\Request;
use Slim\Http\Response;

class PluginController extends SecureBackendController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {

    }


    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}
