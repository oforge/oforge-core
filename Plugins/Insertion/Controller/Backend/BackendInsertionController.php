<?php

namespace Insertion\Controller\Backend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\FrontendUserService;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFormsService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Forge\ForgeEntityManager;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Helper\SessionHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use phpDocumentor\Reflection\Types\This;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;

/**
 * Class BackendInsertionController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 * @EndpointClass(path="/backend/insertions", name="backend_insertions", assetScope="Backend")
 */
class BackendInsertionController extends BaseCrudController {
    /** @var string $model */
    protected $model = Insertion::class;
    /** @var array $modelProperties */
    protected $modelProperties = [

        [
            'name'     => 'link',
            'type'     => CrudDataTypes::STRING,
            'label'    => ['key' => 'plugin_insertion_link', 'default' => 'Link'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderLink.twig',
            ],
        ],
        [
            'name' => 'id',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'     => 'name',
            'type'     => CrudDataTypes::STRING,
            'label'    => ['key' => 'plugin_insertion_name', 'default' => 'Name'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderName.twig',
            ],
        ],
        [
            'name'     => 'title',
            'type'     => CrudDataTypes::STRING,
            'label'    => ['key' => 'plugin_insertion_title', 'default' => 'Title'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderTitle.twig',
            ],
        ],
        [
            'name'     => 'user',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_insertion_user', 'default' => 'User'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderUser.twig',
            ],
        ],
        [
            'name'     => 'type',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_insertion_type', 'default' => 'Type'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Insertion/Backend/BackendInsertion/CRUD/RenderType.twig',
            ],
        ],
        [
            'name'  => 'active',
            'type'  => CrudDataTypes::BOOL,
            'label' => ['key' => 'plugin_insertion_active', 'default' => 'Active'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'moderation',
            'type'  => CrudDataTypes::BOOL,
            'label' => ['key' => 'plugin_insertion_moderation', 'default' => 'Freigegeben'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
    ];


    /** @var array $indexFilter */
    protected $indexFilter = [
        'moderation' => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_insertion_filter_moderation', 'default' => 'Choose Moderation'],
            'list'  => 'getModeration',
        ]
    ];

    public function getModeration(){
        return [
            "1" => I18N::translate("moderation_done"),
            "0" => I18N::translate("moderation_needed")
        ];
    }

    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'createdAt' => CrudGroupByOrder::DESC,
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function createAction(Request $request, Response $response) {
        /**
         * @var $service InsertionTypeService
         */
        $service = Oforge()->Services()->get("insertion.type");

        $result = [];

        $typeId = isset($_POST["type"]) ? $_POST["type"] : null;
        $userId = isset($_POST["user"]) ? $_POST["user"] : null;

        /** @var $userService UserService */
        $userService = Oforge()->Services()->get("frontend.user.management.user");
        if (isset($typeId) && isset($userId)) {
            $result["typeId"]     = $typeId;
            $result["userId"]     = $userId;
            $type                 = $service->getInsertionTypeById($typeId);
            $result["type"]       = $type->toArray();
            $typeAttributes       = $service->getInsertionTypeAttributeTree($typeId);
            $result["attributes"] = $typeAttributes;
            $result["all_attributes"] = $service->getInsertionTypeAttributeMap();

            /**
             * @var $createService InsertionCreatorService
             */
            $createService = Oforge()->Services()->get("insertion.creator");

            /**
             * @var $formsService InsertionFormsService
             */
            $formsService = Oforge()->Services()->get("insertion.forms");

            if ($request->isPost() && isset($_POST["action"]) && $_POST["action"] == "create") {
                $data = $formsService->processPostData($typeId);

                $user = $userService->getUserById($userId);
                /** @var Router $router */
                $router = Oforge()->App()->getContainer()->get('router');

                if (isset($user)) {
                    $processData = $formsService->parsePageData($data);

                    $createService->create($typeId, $user, $processData);
                    $formsService->clearProcessedData($typeId);

                    $uri = $router->pathFor('backend_insertions');

                    Oforge()->View()->Flash()->addMessage("success", I18N::translate('insertion created', 'Insertion successful created'));

                    return $response->withRedirect($uri, 301);
                }

                $data           = $formsService->getProcessedData($typeId);
                $result["data"] = $data;
                $result["all_attributes"] = $service->getInsertionTypeAttributeMap();
            }
        } else {
            $types           = $service->getInsertionTypeTree();
            $result["types"] = $types;

            $result["users"] = $userService->getUsers();
        }

        $result["crud"] = [
            'model' => $this->moduleModelName,
            'item'  => null,
        ];

        Oforge()->View()->assign($result);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/update/{id:\d+}")
     */
    public function updateAction(Request $request, Response $response, $args) {
        $id = $args["id"];
        /**
         * @var $service InsertionService
         */
        $service = Oforge()->Services()->get("insertion");

        /**
         * @var $insertion Insertion
         */
        $insertion = $service->getInsertionById(intval($id));

        /**
         * @var $insertionTypeService InsertionTypeService
         */
        $insertionTypeService = Oforge()->Services()->get("insertion.type");

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect("/404", 301);
        }

        $type                 = $insertion->getInsertionType();
        $typeAttributes       = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $result["attributes"] = $typeAttributes;
        $result["keys"]       = [];
        $result["all_attributes"] = $insertionTypeService->getInsertionTypeAttributeMap();

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
            /**
             * @var $formsService InsertionFormsService
             */
            $formsService = Oforge()->Services()->get("insertion.forms");

            $sessionKey = SessionHelper::generateGuid();
            $formsService->setProcessedData($sessionKey, $result["data"]);

            $data = $formsService->processPostData($sessionKey);
            $data = $formsService->parsePageData($data);

            $updateService->update($insertion, $data);
            $formsService->clearProcessedData($sessionKey);
            $result["data"] = $updateService->getFormData($insertion);
            /** @var Router $router */
            $router = Oforge()->App()->getContainer()->get('router');
            $uri = $router->pathFor('backend_insertions_update', ["id" => $id]);

            Oforge()->View()->Flash()->addMessage("success", I18N::translate('insertion_updated', 'Insertion successful updated'));

            return $response->withRedirect($uri, 301);
        }

        $result["insertion"] = $insertion->toArray(1);
        $result["typeId"]    = $insertion->getInsertionType()->getId();
        $result["userId"]    = $insertion->getUser()->getId();

        Oforge()->View()->assign($result);

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ConfigElementNotFoundException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @EndpointAction(path="/approve/{id:\d+}")
     */
    public function approveInsertionAction(Request $request, Response $response, $args) {
        $insertionId = $args["id"];

        /** @var MailService $mailService */
        $mailService = Oforge()->Services()->get('mail');

        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($insertionId);
        $insertion->setModeration(true);
        $insertionService->entityManager()->update($insertion);

        if($mailService->sendInsertionApprovedInfoMail($insertionId)) {
            Oforge()->View()->Flash()->addMessage('success', 'ID ' . $insertionId . ': Notification has been sent');

        } else {
            Oforge()->View()->Flash()->addMessage('error', 'ID ' . $insertionId . ': Notification could not be sent' );
        }

        return RouteHelper::redirect($response, 'backend_insertions');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/disapprove/{id:\d+}")
     */
    public function disapproveInsertionAction(Request $request, Response $response, $args) {
        $insertionId = $args["id"];

        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($insertionId);
        $insertion->setModeration(false);
        $insertionService->entityManager()->update($insertion);

        // TODO: Tell user why insertion has been disapproved ?

        return RouteHelper::redirect($response, 'backend_insertions');
    }

    public function initPermissions() {
        parent::initPermissions();
        $this->ensurePermissions(['approveInsertionAction','disapproveInsertionAction'], BackendUser::ROLE_MODERATOR);
    }
}
