<?php

namespace Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SystemSettingsController
 *
 * @package Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend
 * @EndpointClass(path="/backend/settings[/]", name="backend_settings", assetScope="Backend")
 */
class SystemSettingsController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $data          = $configService->groups();

        Oforge()->View()->assign(['config' => $data]);
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

}
