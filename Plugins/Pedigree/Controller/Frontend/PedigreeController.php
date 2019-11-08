<?php

namespace Pedigree\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Pedigree\Models\Ancestor;
use Pedigree\Services\PedigreeService;
use Slim\Http\Request;
use Slim\Http\Response;
use \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;

/**
 * Class PedigreeController
 *
 * @package Pedigree\Controller\Frontend
 * @EndpointClass(path="/insertions/pedigree", name="insertion_pedigree", assetScope="Frontend")
 */
class PedigreeController {

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
        /** @var PedigreeService $pedigreeService */
        $pedigreeService = Oforge()->Services()->get('pedigree');
        $names = $pedigreeService->getAllAncestors();
        $names = array_map(function(Ancestor $name) {return $name->getName();}, $names);
        Oforge()->View()->assign(['json' => $names]);
    }
}
