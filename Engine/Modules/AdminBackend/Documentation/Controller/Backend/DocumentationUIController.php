<?php
/**
 * Created by PhpStorm.
 * User: steff
 * Date: 14.12.2018
 * Time: 15:08
 */

namespace Oforge\Engine\Modules\AdminBackend\Documentation\Controller\Backend;


use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Slim\Http\Request;
use Slim\Http\Response;

class DocumentationUIController extends SecureBackendController
{

    public function indexAction(Request $request, Response $response)
    {

    }

    public function generalAction(Request $request, Response $response)
    {

    }

    public function iconsAction(Request $request, Response $response)
    {

    }


    public function buttonsAction(Request $request, Response $response)
    {

    }

    public function modalsAction(Request $request, Response $response)
    {

    }

    public function slidersAction(Request $request, Response $response)
    {

    }

    public function timelineAction(Request $request, Response $response)
    {

    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("generalAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("iconsAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("buttonsAction ", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("modalsAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("slidersAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions("timelineAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}