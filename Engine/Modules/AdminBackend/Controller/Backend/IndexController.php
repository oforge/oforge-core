<?php
namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class IndexController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        /**
         * @var $router Router
         */
        $router = Oforge()->App()->getContainer()->get("router");
        $uri = $router->pathFor("backend_login");

        if (isset($_SESSION['auth'])) {
            $uri = $router->pathFor("backend_dashboard");
        }

        return $response->withRedirect($uri, 302);
    }
}
