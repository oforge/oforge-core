<?php

namespace Insertion\Controller\Frontend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\FrontendUserService;
use Helpdesk\Services\HelpdeskTicketService;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFeedbackService;
use Insertion\Services\InsertionFormsService;
use Insertion\Services\InsertionListService;
use Insertion\Services\InsertionProfileService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Messenger\Models\Conversation;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use function PHPSTORM_META\type;
use ReflectionException;
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
        $service = Oforge()->Services()->get('insertion.type');

        $types = $service->getInsertionTypeTree();

        Oforge()->View()->assign(['types' => $types]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/search")
     */
    public function listAllAction(Request $request, Response $response) {
        /**
         * @var $service InsertionTypeService
         */
        $service = Oforge()->Services()->get('insertion.type');

        $types = $service->getInsertionTypeTree();

        Oforge()->View()->assign(['types' => $types]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/create/{type}/{page}")
     *
     * @throws ORMException
     */
    public function createStepsAction(Request $request, Response $response, $args) {
        $page   = $args['page'];
        $typeId = $args['type'];

        $result = ['page' => $page, 'pagecount' => 5];

        /**
         * @var $service InsertionTypeService
         */
        $service                  = Oforge()->Services()->get('insertion.type');
        $type                     = $service->getInsertionTypeById($typeId);
        $result['type']           = $type->toArray();
        $typeAttributes           = $service->getInsertionTypeAttributeTree($typeId);
        $result['attributes']     = $typeAttributes;
        $result['all_attributes'] = $service->getInsertionTypeAttributeMap();

        /**
         * @var $createService InsertionCreatorService
         */
        $createService = Oforge()->Services()->get('insertion.creator');

        /**
         * @var $formsService InsertionFormsService
         */
        $formsService = Oforge()->Services()->get('insertion.forms');

        if ($request->isPost()) {
            $formsService->processPostData($typeId);
        }

        $data           = $formsService->getProcessedData($typeId);
        $result['data'] = $data;

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/create/{type}")
     */
    public function createTypeAction(Request $request, Response $response, $args) {
        $typeId = $args['type'];

        /**
         * @var $service InsertionTypeService
         */
        $service         = Oforge()->Services()->get('insertion.type');
        $types           = $service->getInsertionTypeTree($typeId);
        $result['types'] = $types;

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/process/{type}")
     */
    public function processStepsAction(Request $request, Response $response, $args) {
        $typeId = $args['type'];
        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get('frontend.user');
        $user        = $userService->getUser();
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        if (isset($user)) {
            /**
             * @var $createService InsertionCreatorService
             */
            $createService = Oforge()->Services()->get('insertion.creator');

            /**
             * @var $formsService InsertionFormsService
             */
            $formsService = Oforge()->Services()->get('insertion.forms');

            if ($request->isPost()) {
                $data = $formsService->processPostData($typeId);
                try {
                    $processData = $formsService->parsePageData($data);

                    $createService->create($typeId, $user, $processData);

                    $uri = $router->pathFor('insertions_feedback');

                    $formsService->clearProcessedData($typeId);

                    return $response->withRedirect($uri, 301);
                } catch (Exception $exception) {
                    Oforge()->Logger()->get()->error('insertion_creation', $data);
                    Oforge()->Logger()->get()->error('insertion_creation_stack', $exception->getTrace());

                    Oforge()->View()->Flash()->addMessage('error', 'server_error');
                    $uri = $router->pathFor('insertions_createSteps', ['type' => $typeId, 'page' => '5']);

                    return $response->withRedirect($uri, 301);
                }
            }
        } else {
            Oforge()->View()->Flash()->addMessage('error', 'missing_user');
            $uri = $router->pathFor('insertions_createSteps', ['type' => $typeId, 'page' => '5']);

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
            $feedbackService = Oforge()->Services()->get('insertion.feedback');
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
     * @throws ORMException
     */
    public function listingAction(Request $request, Response $response, $args) {
        $typeIdOrName = $args['type'];

        $result = [];

        /**
         * @var $service InsertionTypeService
         */
        $service = Oforge()->Services()->get('insertion.type');
        /**
         * @var $type InsertionType
         */
        $type = $service->getInsertionTypeByName($typeIdOrName);
        if ($type == null) {
            $type = $service->getInsertionTypeById($typeIdOrName);
        }

        if (!isset($type) || $type == null) {
            return $response->withRedirect('/404', 301);
        }

        $typeAttributes           = $service->getInsertionTypeAttributeTree($type->getId());
        $result['attributes']     = $typeAttributes;
        $result["all_attributes"] = $service->getInsertionTypeAttributeMap();
        $result['keys']           = [];
        $result['typeId']         = $args['type'];
        $result['type']           = $type->toArray(0);
        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result['keys'][$key->getName()] = $key->toArray(0);
        }

        /**
         * @var $listService InsertionListService
         */
        $listService = Oforge()->Services()->get('insertion.list');

        $radius = $listService->saveSearchRadius($_GET);

        print_r($_GET);

        $result['search'] = $listService->search($type->getId(), array_merge($_GET, $radius));

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/detailsearch/{type}")
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function detailSearchAction(Request $request, Response $response, $args) {
        $typeIdOrName = $args['type'];
        /** @var $service InsertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');

        /** @var $type InsertionType */
        $type = $insertionTypeService->getInsertionTypeByName($typeIdOrName);
        if ($type == null) {
            $type = $insertionTypeService->getInsertionTypeById($typeIdOrName);
        }

        if (!isset($type) || $type == null) {
            return $response->withRedirect('/404', 301);
        }

        $typeAttributes           = $insertionTypeService->getInsertionTypeAttributeTree($type->getId());
        $result['attributes']     = $typeAttributes;
        $result['keys']           = [];
        $result['typeId']         = $args['type'];
        $result['all_attributes'] = $insertionTypeService->getInsertionTypeAttributeMap();

        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result['keys'][$key->getName()] = $key->toArray(0);
        }

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/detail/{id}")
     */
    public function detailAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /**
         * @var $service InsertionService
         */
        $service = Oforge()->Services()->get('insertion');
        /**
         * @var $insertion Insertion
         */
        $insertion = $service->getInsertionById(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        $values = [];

        foreach ($insertion->toArray()['values'] as $value) {
            $id     = $value['attributeKey'];
            $values = $values + [$id => $value];
        }

        Oforge()->View()->assign(['values' => $values]);

        if (!($insertion->isActive() && $insertion->isModeration())) {
            $auth = '';
            if (isset($_SESSION['auth'])) {
                $auth = $_SESSION['auth'];
            }

            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);

            if ($user['type'] != BackendUser::class && $insertion->getUser()->getId() != $user['id']) {
                return $response->withRedirect('/404', 301);
            }
        }

        Oforge()->View()->assign(["insertion" => $insertion->toArray(3, ['user' => ['*', 'id']])]);

        /**
         * @var $service InsertionProfileService
         */
        $service = Oforge()->Services()->get('insertion.profile');

        $profile = $service->get($insertion->getUser()->getId());
        if (isset($profile)) {
            Oforge()->View()->assign(["profile" => $profile->toArray()]);
        }

        /** @var $insertionTypeService InsertionTypeService */
        $insertionTypeService = Oforge()->Services()->get("insertion.type");

        $typeAttributes  = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $insertionValues = [];
        foreach ($insertion->getValues() as $value) {
            if (isset($insertionValues[$value->getAttributeKey()->getId()])) {
                if (is_array($insertionValues[$value->getAttributeKey()->getId()])) {
                    $insertionValues[$value->getAttributeKey()->getId()][] = $value->getValue();
                } else {
                    $insertionValues[$value->getAttributeKey()->getId()] = [$insertionValues[$value->getAttributeKey()->getId()], $value->getValue()];
                }
            } else {
                $insertionValues[$value->getAttributeKey()->getId()] = $value->getValue();
            }
        }
        Oforge()->View()->assign(["attributes" => $typeAttributes]);
        Oforge()->View()->assign(["all_attributes" => $insertionTypeService->getInsertionTypeAttributeMap()]);
        Oforge()->View()->assign(["insertion_values" => $insertionValues]);

    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/edit/{id}")
     *
     * @throws ORMException
     */
    public function editAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /**
         * @var $service InsertionService
         */
        $service   = Oforge()->Services()->get('insertion');
        $insertion = $service->getInsertionById(intval($id));

        /**
         * @var $insertionTypeService InsertionTypeService
         */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');

        /**
         * @var $userService FrontendUserService
         */
        $userService = Oforge()->Services()->get('frontend.user');
        $user        = $userService->getUser();

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        if ($user == null || $insertion->getUser()->getId() != $user->getId()) {
            return $response->withRedirect('/401', 301);
        }

        $type                     = $insertion->getInsertionType();
        $typeAttributes           = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $result['attributes']     = $typeAttributes;
        $result['keys']           = [];
        $result['all_attributes'] = $insertionTypeService->getInsertionTypeAttributeMap();
        /**
         * @var $attribute InsertionTypeAttribute
         */
        foreach ($type->getAttributes() as $attribute) {
            $key                             = $attribute->getAttributeKey();
            $result['keys'][$key->getName()] = $key->toArray(0);
        }

        /**
         * @var $updateService InsertionUpdaterService
         */
        $updateService = Oforge()->Services()->get('insertion.updater');

        $result['data'] = $updateService->getFormData($insertion);
        /**
         * @var $formsService InsertionFormsService
         */
        $formsService = Oforge()->Services()->get('insertion.forms');

        if ($request->isPost()) {
            $data = $formsService->processPostData('insertion' . $insertion->getId());
            $data = $formsService->parsePageData($data);

            $updateService->update($insertion, $data);

            $insertion      = $service->getInsertionById(intval($id));
            $result['data'] = $updateService->getFormData($insertion);
            $formsService->clearProcessedData('insertion' . $insertion->getId());
        }

        $result['insertion'] = $insertion->toArray(1);

        Oforge()->View()->assign($result);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction(path="/profile/{id}")
     *
     * @throws ORMException
     */
    public function profileAction(Request $request, Response $response, $args) {
        /**
         * @var $service InsertionProfileService
         */
        $service = Oforge()->Services()->get('insertion.profile');

        $result = $service->getById($args['id']);

        if ($result == null) {
            return $response->withRedirect('/404', 301);
        }

        /**
         * @var $listService InsertionListService
         */
        $listService = Oforge()->Services()->get('insertion.list');
        $insertions  = $listService->getUserInsertions($result->getUser()->getId(), 1, 20);

        Oforge()->View()->assign(['profile' => $result->toArray(), 'insertions' => $insertions]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @EndpointAction(path="/contact/{id}")
     */
    public function contactAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /** @var $service InsertionService */
        $insertionService = Oforge()->Services()->get('insertion');
        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        if ($request->isPost()) {
            $body    = $request->getParsedBody();
            $message = $body['message'];

            /** @var $userService FrontendUserService */
            $userService = Oforge()->Services()->get('frontend.user');

            $user = $userService->getUser();

            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');

            if (is_null($user)) {
                $uri = $router->pathFor('frontend_login');

                return $response->withRedirect($uri, 302);
            }

            /** @var FrontendMessengerService $messengerService */
            $messengerService = Oforge()->Services()->get('frontend.messenger');
            /** @var Conversation $conversation */
            $conversation = $messengerService->checkForConversation($user->getId(), $insertion->getUser()->getId(), 'insertion', $insertion->getId());

            if (is_null($conversation)) {
                $data = [
                    'requester'    => $user->getId(),
                    'requested'    => $insertion->getUser()->getId(),
                    'type'         => 'insertion',
                    'targetId'     => $insertion->getId(),
                    'title'        => $insertion->getContent()[0]->getTitle(),
                    'firstMessage' => $message,
                ];

                $conversation = $messengerService->createNewConversation($data);
            }
            $uri = $router->pathFor('frontend_account_messages', ['id' => $conversation->getId()]);

            return $response->withRedirect($uri, 302);
        }

        Oforge()->View()->assign(['insertion' => $insertion->toArray(2)]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws ReflectionException
     * @EndpointAction(path="/report/{id}")
     */
    public function reportAction(Request $request, Response $response, $args) {
        /** @var HelpdeskTicketService $crud */
        $helpdeskService = Oforge()->Services()->get('helpdesk.ticket');
        $reportTypes     = $helpdeskService->getIssueTypesByGroup('report');

        $id = $args['id'];
        /** @var $service InsertionService */
        $insertionService = Oforge()->Services()->get('insertion');
        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        if ($request->isPost()) {
            $body      = $request->getParsedBody();
            $issueType = $body['issueType'];
            $message   = $body['message'];

            /** @var $userService FrontendUserService */
            $userService = Oforge()->Services()->get('frontend.user');
            $user        = $userService->getUser();

            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');

            if (is_null($user)) {
                $uri = $router->pathFor('frontend_login');

                return $response->withRedirect($uri, 302);
            }

            /** @var HelpdeskTicketService $helpdeskService */
            $helpdeskService = Oforge()->Services()->get('helpdesk.ticket');

            $conversation = $helpdeskService->createNewTicket($user->getId(), $issueType, 'Report of Insertion: ' . $insertion->getId(), $message);

            $uri = $router->pathFor('frontend_account_messages', ['id' => $conversation->getId()]);

            return $response->withRedirect($uri, 302);
        }

        Oforge()->View()->assign([
            'insertion'   => $insertion->toArray(2),
            'reportTypes' => $reportTypes,
        ]);
    }

    public function initPermissions() {
        $this->ensurePermissions('accountListAction', User::class);
    }
}
