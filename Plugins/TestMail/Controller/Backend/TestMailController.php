<?php

namespace TestMail\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * Class TestMailController
 *
 * @package TestMail\Controller\Backend
 * @EndpointClass(path="/backend/testmail", name="backend_testmail", assetScope="Backend")
 *
 */
class TestMailController extends SecureBackendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function indexAction(Request $request, Response $response) {

        $router                     = Oforge()->Container()->get('router');
        $showMailLink               = $router->pathFor('backend_showmail');
        $sendMailLink               = $router->pathFor('backend_sendmail');
        // TODO: get all template files in active template
        Oforge()->View()->assign(['showMailLink' => $showMailLink, 'sendMailLink' => $sendMailLink]);

    }

    public function sendAction(Request $request, Response $response) {
        //
    }
    public function showAction(Request $request, Response $response) {
        //
    }
    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'sendAction',
            'showAction',
        ], BackendUser::ROLE_ADMINISTRATOR);
    }

}
