<?php

namespace TestMail\Controller\Frontend;

use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;


/**
 * Class TestMailController
 *
 * @package TestMail\Controller\Frontend
 * @EndpointClass(path="/testmail", name="testmail", assetScope="Frontend")
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
        $showMailLink               = $router->pathFor('showmail');
        $sendMailLink               = $router->pathFor('sendmail');
        Oforge()->View()->assign(['showMailLink' => $showMailLink, 'sendMailLink' => $sendMailLink]);

        return $response;

    }

    public function sendAction(Request $request, Response $response) {
        //
    }
    public function showAction(Request $request, Response $response) {
        //
    }

}
