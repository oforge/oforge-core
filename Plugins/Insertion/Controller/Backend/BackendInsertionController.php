<?php

namespace Insertion\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Services\InsertionCreatorService;
use Insertion\Services\InsertionFormsService;
use Insertion\Services\InsertionService;
use Insertion\Services\InsertionTypeService;
use Insertion\Services\InsertionUpdaterService;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Helper\SessionHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use ReflectionException;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

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
        ],# link
        [
            'name' => 'id',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index' => 'readonly',
            ],
        ],# id
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
        ],# name
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
        ],# title
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
        ],# user
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
        ],# type
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
        ],# active
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
        ],# moderation
        [
            'name'  => 'createdAt',
            'type'  => CrudDataTypes::DATETIME,
            'label' => ['key' => 'plugin_insertion_createdAt', 'default' => 'Erstellt'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],# createdAt
        [
            'name'  => 'updatedAt',
            'type'  => CrudDataTypes::DATETIME,
            'label' => ['key' => 'plugin_insertion_updatedAt', 'default' => 'Aktualisiert'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],# updatedAt
        [
            'name'  => 'views',
            'type'  => CrudDataTypes::INT,
            'lable' => ['key' => 'plugin_insertion_views', 'default' => 'Views'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],# views
        [
            'name'  => 'spam',
            'type'  => CrudDataTypes::BOOL,
            'lable' => ['key' => 'plugin_insertion_spam', 'default' => 'Spam'],
            'crud' => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly'
            ]
        ], # spam
        [
            'name'  => 'deactivationCause',
            'type'  => CrudDataTypes::STRING,
            'lable' => ['key' => 'plugin_insertion_deactivationCause', 'default' => 'Deaktivierungs-Grund'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ]
        ], #deactivationCause

    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'moderation' => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_insertion_filter_moderation', 'default' => 'Choose Moderation'],
            'list'  => 'getModerationState',
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'createdAt' => CrudGroupByOrder::DESC,
    ];

    public function getModerationState() {
        return [
            '1' => I18N::translate('moderation_done'),
            '0' => I18N::translate('moderation_needed'),
        ];
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
        /**  @var InsertionTypeService $service */
        $service = Oforge()->Services()->get('insertion.type');
        /** @var $userService UserService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');

        $result = [];

        $typeId = $request->getParsedBodyParam('type');
        $userId = $request->getParsedBodyParam('user');

        if (isset($typeId) && isset($userId)) {
            $result['typeId']         = $typeId;
            $result['userId']         = $userId;
            $type                     = $service->getInsertionTypeById($typeId);
            $result['type']           = $type->toArray();
            $typeAttributes           = $service->getInsertionTypeAttributeTree($typeId);
            $result['attributes']     = $typeAttributes;
            $result['all_attributes'] = $service->getInsertionTypeAttributeMap();

            /** @var InsertionCreatorService $createService */
            $createService = Oforge()->Services()->get('insertion.creator');
            /** @var InsertionFormsService $formsService */
            $formsService = Oforge()->Services()->get('insertion.forms');

            if ($request->isPost() && $request->getParsedBodyParam('action') === 'create') {
                $data = $formsService->processPostData($typeId);
                $user = $userService->getUserById($userId);
                if (isset($user)) {
                    $processData = $formsService->parsePageData($data);

                    $createService->create($typeId, $user, $processData);
                    $formsService->clearProcessedData($typeId);

                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('insertion created', 'Insertion successful created'));

                    return $this->redirect($response, 'index');
                }
                $data                     = $formsService->getProcessedData($typeId);
                $result['data']           = $data;
                $result['all_attributes'] = $service->getInsertionTypeAttributeMap();
            }
        } else {
            $types           = $service->getInsertionTypeTree();
            $result['types'] = $types;
            $result['users'] = $userService->getUsers();
        }
        $result['crud'] = [
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
     * @throws OptimisticLockException
     * @throws ReflectionException
     * @EndpointAction(path="/update/{id:\d+}")
     */
    public function updateAction(Request $request, Response $response, $args) {
        $id = $args['id'];
        /** @var InsertionService $service */
        $service = Oforge()->Services()->get('insertion');
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        /** @var Insertion $insertion */
        $insertion = $service->getInsertionById(intval($id));

        if (!isset($insertion) || $insertion == null) {
            return $response->withRedirect('/404', 301);
        }

        $type                     = $insertion->getInsertionType();
        $typeAttributes           = $insertionTypeService->getInsertionTypeAttributeTree($insertion->getInsertionType()->getId());
        $result['attributes']     = $typeAttributes;
        $result['keys']           = [];
        $result['all_attributes'] = $insertionTypeService->getInsertionTypeAttributeMap();

        /** @var InsertionTypeAttribute $attribute */
        foreach ($type->getAttributes() as $attribute) {
            $key = $attribute->getAttributeKey();

            $result['keys'][$key->getName()] = $key->toArray(0);
        }

        /** @var InsertionUpdaterService $updateService */
        $updateService  = Oforge()->Services()->get('insertion.updater');
        $result['data'] = $updateService->getFormData($insertion);
        if ($request->isPost()) {
            /** @var InsertionFormsService $formsService */
            $formsService = Oforge()->Services()->get('insertion.forms');

            $sessionKey = SessionHelper::generateGuid();
            $formsService->setProcessedData($sessionKey, $result['data']);

            $data = $formsService->processPostData($sessionKey, !empty($insertion->getMedia()));
            $data = $formsService->parsePageData($data);

            $updateService->update($insertion, $data);
            $formsService->clearProcessedData($sessionKey);
            $result['data'] = $updateService->getFormData($insertion);

            Oforge()->View()->Flash()->addMessage('success', I18N::translate('insertion_updated', 'Insertion successful updated'));

            return RouteHelper::redirect($response, 'backend_insertions_update', ['id' => $id]);
        }

        $result['insertion'] = $insertion->toArray(1);
        $result['typeId']    = $insertion->getInsertionType()->getId();
        $result['type']      = $insertion->getInsertionType()->toArray(1);
        $result['userId']    = $insertion->getUser()->getId();

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
     * @throws ConfigOptionKeyNotExistException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction(path="/approve/{id:\d+}")
     */
    public function approveInsertionAction(Request $request, Response $response, $args) {
        $insertionId = $args['id'];

        /** @var MailService $mailService */
        $mailService = Oforge()->Services()->get('mail');
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($insertionId);
        $insertion->setModeration(true);
        $insertionService->entityManager()->update($insertion);

        if ($mailService->sendInsertionApprovedInfoMail($insertionId)) {
            Oforge()->View()->Flash()->addMessage('success', sprintf('ID %s: Notification has been sent', $insertionId));
        } else {
            Oforge()->View()->Flash()->addMessage('error', sprintf('ID %s: Notification could not be sent', $insertionId));
        }

        return $this->redirect($response, 'index');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/disapprove/{id:\d+}")
     */
    public function disapproveInsertionAction(Request $request, Response $response, array $args) {
        $insertionId = $args['id'];

        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($insertionId);
        $insertion->setModeration(false);
        $insertionService->entityManager()->update($insertion);

        // TODO: Tell user why insertion has been disapproved ?

        return $this->redirect($response, 'index');
    }

    public function initPermissions() {
        parent::initPermissions();
        $this->ensurePermissions([
            'approveInsertionAction',
            'disapproveInsertionAction',
        ], BackendUser::ROLE_MODERATOR);
    }

}
