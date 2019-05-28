<?php

namespace Insertion\Controller\Frontend;

use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\Event\OnFlushEventArgs;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserService;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFeedbackService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
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
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        if (isset($user)) {
            /**
             * @var $createService InsertionCreatorService
             */
            $createService = Oforge()->Services()->get("insertion.creator");

            if ($request->isPost()) {
                $data = $createService->processPostData($typeId);
                try {
                    $processData = $createService->parsePageData($data);

                    $createService->create($typeId, $user, $processData);

                    $uri = $router->pathFor('insertions_feedback');

                    $createService->clearProcessedData($typeId);

                    return $response->withRedirect($uri, 301);
                } catch (\Exception $exception) {
                    Oforge()->Logger()->get()->error("insertion_creation", $data);
                    Oforge()->Logger()->get()->error("insertion_creation_stack", $exception->getTrace());

                    Oforge()->View()->Flash()->addMessage("error", "server_error");
                    $uri = $router->pathFor('insertions_createSteps', ["type" => $typeId, "page" => "5"]);

                    return $response->withRedirect($uri, 301);
                }
            }

        } else {
            Oforge()->View()->Flash()->addMessage("error", "missing_user");
            $uri = $router->pathFor('insertions_createSteps', ["type" => $typeId, "page" => "5"]);

            return $response->withRedirect($uri, 301);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/feedback")
     */
    public function feedbackAction(Request $request, Response $response) {
        if ($request->isPost()) {
            /**
             * @var $feedbackService InsertionFeedbackService
             */
            $feedbackService = Oforge()->Services()->get("insertion.feedback");
            $feedbackService->savePostData();

            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri    = $router->pathFor('insertions_success');

            return $response->withRedirect($uri, 301);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/success")
     */
    public function successAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/search/{type}")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function listingAction(Request $request, Response $response, $args) {
        $typeIdOrName = $args["type"];

        $result = [];

        /**
         * @var $service InsertionTypeService
         */
        $service = Oforge()->Services()->get("insertion.type");
        /**
         * @var $type InsertionType
         */
        $type = $service->getInsertionTypeByName($typeIdOrName);
        if ($type == null) {
            $type = $service->getInsertionTypeById($typeIdOrName);
        }

        if (!isset($type) || $type == null) {
            return $response->withRedirect("/404", 301);
        }

        $typeAttributes       = $service->getInsertionTypeAttributeTree($type->getId());
        $result["attributes"] = $typeAttributes;
        $result["keys"]       = [];
        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result["keys"][$key->getName()] = $key->toArray(0);
        }

        /**
         * @var $listService InsertionListService
         */
        $listService      = Oforge()->Services()->get("insertion.list");
        $result["search"] = $listService->search($type->getId(), $_GET);

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/detail/{id}")
     */
    public function detailAction(Request $request, Response $response, $args) {
        $id = $args["id"];
        /**
         * @var $service InsertionService
         */
        $service   = Oforge()->Services()->get("insertion");
        $insertion = $service->getInsertionById(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect("/404", 301);
        }

        Oforge()->View()->assign(["insertion" => $insertion->toArray(3)]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/edit/{id}")
     *
     * @throws \Doctrine\ORM\ORMException
     */
    public function editAction(Request $request, Response $response, $args) {
        $id = $args["id"];
        /**
         * @var $service InsertionService
         */
        $service   = Oforge()->Services()->get("insertion");
        $insertion = $service->getInsertionById(intval($id));

        /**
         * @var $insertionTypeService InsertionTypeService
         */
        $insertionTypeService = Oforge()->Services()->get("insertion.type");

        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get("frontend.user");
        $user        = $userService->getUser();

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect("/404", 301);
        }

        if ($user == null || $insertion->getUser()->getId() != $user->getId()) {
            return $response->withRedirect("/401", 301);
        }

        $type                 = $insertion->getInsertionType();
        $typeAttributes       = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $result["attributes"] = $typeAttributes;
        $result["keys"]       = [];
        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result["keys"][$key->getName()] = $key->toArray(0);
        }

        /**
         * @var $updateService InsertionUpdaterService
         */
        $updateService = Oforge()->Services()->get("insertion.updater");

        $result["data"] = $updateService->getFormData($insertion);

        if ($request->isPost()) {
            $data = $updateService->parsePageData($_POST);

            $updateService->update($insertion, $data);
            $result["data"] = $updateService->getFormData($insertion);

        }

        $result["insertion"] = $insertion->toArray(1);

        Oforge()->View()->assign($result);
    }

    public function initPermissions() {
        $this->ensurePermissions('accountListAction', User::class);
    }
}
