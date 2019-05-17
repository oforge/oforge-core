<?php

namespace Insertion\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionTypeService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MessengerController
 *
 * @package Messenger\Controller\Frontend
 * @EndpointClass(path="/insertions", name="insertions", assetScope="Frontend")
 */
class FrontendInsertionController extends SecureFrontendController {

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
     * @EndpointAction(path="/create")
     */
    public function createAction(Request $request, Response $response) {
        /**
         * @var $service InsertionTypeService
         */
        $service = Oforge()->Services()->get("insertion.type");

        $types = $service->getInsertionTypeTree();

        Oforge()->View()->assign(["types" => $types]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/create/{type}/{page}")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function createStepsAction(Request $request, Response $response, $args) {
        $page   = $args["page"];
        $typeId = $args["type"];

        $result = ["page" => $page, "pagecount" => 5];

        /**
         * @var $service InsertionTypeService
         */
        $service        = Oforge()->Services()->get("insertion.type");
        $type           = $service->getInsertionTypeById($typeId);
        $result["type"] = $type;

        /**
         * @var $createService InsertionCreatorService
         */
        $createService = Oforge()->Services()->get("insertion.creator");

        if ($request->isPost()) {
            $createService->processPostData($typeId);
        }

        $data           = $createService->getProcessedData($typeId);
        $result["data"] = $data;

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/create/{type}")
     */
    public function createTypeAction(Request $request, Response $response, $args) {
        $typeId = $args["type"];

        /**
         * @var $service InsertionTypeService
         */
        $service         = Oforge()->Services()->get("insertion.type");
        $types           = $service->getInsertionTypeTree($typeId);
        $result["types"] = $types;

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function viewAction(Request $request, Response $response) {
    }
}
