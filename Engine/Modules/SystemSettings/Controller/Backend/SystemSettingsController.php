<?php
/**
 * Created by PhpStorm.
 * User: Steffen
 * Date: 15.12.2018
 * Time: 16:40
 */

namespace Oforge\Engine\Modules\SystemSettings\Controller\Backend;


use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Slim\Http\Request;
use Slim\Http\Response;

class SystemSettingsController extends SecureBackendController
{

    public function initPermissions()
    {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

    public function indexAction(Request $request, Response $response)
    {
        //$settingsService = Oforge()->Services()->get("system.settings.service");
    }
}