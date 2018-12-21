<?php

namespace Oforge\Engine\Modules\SystemSettings\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Slim\Http\Request;
use Slim\Http\Response;


class SystemSettingsGroupController extends SecureBackendController
{
    public function indexAction(Request $request, Response $response, $args)
    {
        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get("config");

        $config = $configService->list($args['group']);

        Oforge()->View()->assign(["page_header" => $args['group'], "config" => $config, "groupname" => $args['group']]);
    }

    public function initPermissions()
    {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}