<?php

namespace Test\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class HomeController
 *
 * @package Test\Controller\Frontend
 * @EndpointClass(path="/test/home", name="frontend_test_home", assetScope="Frontend")
 */
class HomeController extends AbstractController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        Oforge()->View()->assign([
            'greeting' => 'Hello from the TestPlugin',
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function jsonAction(Request $request, Response $response) {
        Oforge()->View()->assign([
            'blub'     => 'test',
            'language' => $request->getAttribute('language_id'),
        ]);
    }

}
