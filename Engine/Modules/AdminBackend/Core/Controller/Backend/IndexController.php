<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class IndexController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Controller\Backend
 * @EndpointClass(path="/backend[/]", name="backend", assetScope="Backend")
 */
class IndexController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri    = $router->pathFor('backend_login');
        if (isset($_SESSION['auth'])) {
            $uri = $router->pathFor('backend_dashboard');
        }

        return $response->withRedirect($uri, 302);
    }

}
