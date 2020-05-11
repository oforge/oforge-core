<?php

namespace TestPlugin\Controller\Frontend;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;
use \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;

/**
 * Class TestController
 *
 * @package TestPlugin\Controller\Frontend
 * @EndpointClass(path="/insertions/pedigree", name="insertion_pedigree", assetScope="Frontend")
 */
class FrontendTestController {

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {

    }
    /**
     * @param Request $request
     * @param Response $response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/all")
     */
    public function getAllAction(Request $request, Response $response) {
        /** @var TestService $testService */
        $pedigreeService = Oforge()->Services()->get('testplugin.testservice');
        $names = $pedigreeService->getAllAncestors();
        $names = array_map(function(Ancestor $name) {return $name->getName();}, $names);
        Oforge()->View()->assign(['json' => $names]);
    }
}
