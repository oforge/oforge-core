<?php
namespace Test\Controller\Frontend;

use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends AbstractController {

    public function indexAction(Request $request, Response $response) {
        $data = ['greeting' => 'Hello from the TestPlugin'];
        Oforge()->View()->assign($data);
    }
    
    public function jsonAction(Request $request, Response $response) {
        $data = array("blub" => "test", "language" => $request->getAttribute('language_id'));
        Oforge()->View()->assign($data);
    }
}
