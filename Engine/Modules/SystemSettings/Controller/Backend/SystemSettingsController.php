<?php

namespace Oforge\Engine\Modules\SystemSettings\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Slim\Http\Request;
use Slim\Http\Response;

class SystemSettingsController extends SecureBackendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get("config");

        $data = $configService->groups();

        Oforge()->View()->assign(["config" => $data]);
    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

}