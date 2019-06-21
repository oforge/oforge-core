<?php

namespace TestMail\Controller\Backend;

use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * Class TestMailController
 *
 * @package TestMail\Controller\Backend
 * @EndpointClass(path="/backend/testmail", name="backend_testmail", assetScope="Backend")
 *
 */
class TestMailController extends AbstractController {
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
        Oforge()->View()->assign(['showMailLink' => $showMailLink, 'sendMailLink' => $sendMailLink]);

    }

    public function sendAction(Request $request, Response $response) {
        //
    }
    public function showAction(Request $request, Response $response) {
        //
    }

}
