<?php
namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Services\EndpointService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class IndexController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        $auth = $request->getCookieParam("authorization");
    
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");

        if (isset($auth)) {
            $uri = $router->pathFor("backend_dashboard");
        } else {
            $uri = $router->pathFor("backend_login");
        }

        return $response->withRedirect($uri, 302);
    }
}
