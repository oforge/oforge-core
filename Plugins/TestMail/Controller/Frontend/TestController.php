<?php

namespace TestMail\Controller\Frontend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * Class TestMailController
 *
 * @package TestMail\Controller\Frontend
 * @EndpointClass(path="/frontend/test", name="frontend_ms_test", assetScope="Backend")
 *
 */
class TestController extends SecureBackendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function indexAction(Request $request, Response $response) {



        Oforge()->View()->assign(['tim' => "tim"]);
    }


    public function initPermissions() {

    }

}
