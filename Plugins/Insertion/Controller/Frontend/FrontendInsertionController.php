<?php

namespace Insertion\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Services\FrontendUserService;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionTypeService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

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
        $service              = Oforge()->Services()->get("insertion.type");
        $type                 = $service->getInsertionTypeById($typeId);
        $result["type"]       = $type->toArray();
        $typeAttributes       = $service->getInsertionTypeAttributeTree($typeId);
        $result["attributes"] = $typeAttributes;
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
     * @EndpointAction(path="/process/{type}")
     */
    public function processStepsAction(Request $request, Response $response, $args) {
        $typeId = $args["type"];
        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        if (isset($user)) {
            /**
             * @var $createService InsertionCreatorService
             */
            $createService = Oforge()->Services()->get("insertion.creator");

            if ($request->isPost()) {
                $data = $createService->processPostData($typeId);
                $createService->create($typeId, $user, $data);
            }

        } else {
            Oforge()->View()->Flash()->addMessage("error", "missing_user");
            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri    = $router->pathFor('insertions_createSteps', ["type" => $typeId, "page" => "5"]);

            return $response->withRedirect($uri, 301);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function viewAction(Request $request, Response $response) {
    }
}
