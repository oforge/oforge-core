<?php
namespace Oforge\Engine\Modules\AdminBackend\Controller\Backend;

use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Services\EndpointService;
use Slim\Http\Request;
use Slim\Http\Response;

class IndexController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        $auth = $request->getCookieParam("authorization");
    
        // $uri = $request->getUri()->withPath($this->router->pathFor('home')); return $response->withRedirect((string)$uri);
       
        $uri = $request->getUri();
        
        if (isset($auth)) {
            $uri = $uri->withPath('/backend/dashboard');
        } else {
            $uri = $uri->withPath('/backend/login');
        }
        return $response->withRedirect((string)$uri, 302);
    }
}
