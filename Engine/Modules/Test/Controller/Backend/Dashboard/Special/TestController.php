<?php
namespace Oforge\Engine\Modules\Test\Controller\Backend\Dashboard\Special;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

class TestController extends AbstractController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return \Psr\Http\Message\ResponseInterface|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Twig_Error_Loader
     */
    public function indexAction(Request $request, Response $response) {
        // Some very difficult algorithm to fetch data


        $data = ["greeting" => "Hello Backend Dashboard Special TestController"];


        //Two-Face: "send the Data to the TemplateEngine... or not!
        return Oforge()->Templates()->render($request, $response, $data);
    }
}
