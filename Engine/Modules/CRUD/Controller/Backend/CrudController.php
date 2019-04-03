<?php

namespace Oforge\Engine\Modules\CRUD\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CrudController
 *
 * @package Oforge\Engine\Modules\CRUD\Controller\Backend
 */
class CrudController extends SecureBackendController {
    /** @var string $model */
    protected $model = null;
    /**
     * Define model properties (editor, visibility in index table and crate/update forms) e.g.
     *      protected $modelProperties = [
     *          [
     *              'name'      => 'id',    // Property name. Required
     *              'label'     => 'backend_crud_property__<Module><ModelName>_<propertyName>', //own i18n key
     *              'type'      => CrudDataTypes::..., // Required
     *              'objectKey' => ...,     // If type = object. Access key of object. (Required*)
     *              'crud'      => [        // default = off (not rendered). (Required)
     *                  'index'     => 'off|readonly|editable',
     *                  'view'      => 'off|readonly|editable',
     *                  'create'    => 'off|readonly|editable',
     *                  'update'    => 'off|readonly|editable',
     *                  'delete'    => 'off|readonly|editable',
     *              ],
     *              'list'          => [ // If type = select. Name of a protected function to create a dynamic array (e.g. 'getListUsers', or a static array. (Required)
     *                  <value> => <option label e.g. 'backend_crud_property__<name>_<value>'>  // value => (i18n-)label pair
     *              ],
     *              'editor' => [       // Configuration for field editor.
     *                  'default'       => '',      // Default value. (Optional)
     *                  'custom'        => '...'    // If type = custom. Twig path for include.
     *                  'pattern'       => '...',   // If type = string. (Optional)
     *                  'placeholder'   => ...,     //. (Optional)
     *                  'maxlength'     => ...,     // If type = string|text. (Optional)
     *                  'min'           => '...',   // If type = int|float|currency. (Optional)
     *                  'max"           => ...,     // If type = string|text. (Optional)
     *                  'step"          => ...,     // If type = string|text. (Optional)
     *                  'multiple'      => false,   // If type = select. (Optional)
     *                  'size'          => ...,     // If type = select. (Optional)
     *              ],
     *              'renderer' => [ // Configuration for renderer
     *                  'alignment' => 'left|center|right', // If type = int|float|currency then default = right otherwise left. (Optional)
     *                  'custom'    => '...'        // If type = custom. Twig path for include.
     *              ],
     *          ], ...
     *      ];
     *
     * @var array|null $modelProperties
     */
    protected $modelProperties = null;
    /**
     * Enable or disable crud actions for this model with
     *      protected $crudActions = [
     *          'create'    => true, // enable create button and view
     *          'update'    => true, // enable update button and view
     *          'delete'    => true, // enable delete button and view
     *      ];
     *
     * @var array $crudActions Keys of 'add|edit|delete'
     */
    protected $crudActions = [
        'index'  => true,
        'create' => true,
        'view'   => true,
        'update' => true,
        'delete' => true,
        // TODO action duplicate???
    ];
    /**
     * @var int $crudPermission
     */
    protected $crudPermission = BackendUser::ROLE_MODERATOR;
    /** @var GenericCrudService $crudService */
    protected $crudService;

    /**
     * CrudController constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        $this->crudService = Oforge()->Services()->get('crud');
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     * @throws \ReflectionException
     */
    public function indexAction(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();
            if (!empty($params) && $request->isPost()) {
                if (isset($params['action'])) {
                    switch (strtolower($params['action'])) {
                        case 'create':
                            $this->create($request, $response);
                            break;
                        case 'update':
                            $this->update($request, $response);
                            break;
                        case 'delete':
                            $this->delete($request, $response);
                            break;
                    }
                }
            }
        }
        $this->index($request, $response);
    }

    public function createAction(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();
            if ($request->isPost() && !empty($params)) {
                try {
                    $this->crudService->create($this->model, $params['data']);
                    Oforge()->View()->assign([
                        'message' => [
                            'type'     => 'success',
                            'body'     => 'backend_message_create_success_body',
                            'headline' => 'backend_message_create_success_headline',
                        ],
                    ]);
                } catch (\Exception $e) {
                    Oforge()->View()->assign([
                        'message' => [
                            'type'     => 'danger',
                            'body'     => 'backend_message_create_error_body',
                            'headline' => 'backend_message_create_error_headline',
                            'exception' => [
                                'message' => $e->getMessage(),
                                'trace' => $e->getTrace(),
                            ]
                        ],
                    ]);
                }
            }
            list($properties, $hasEditors) = $this->filterPropertiesFor('create');
            Oforge()->View()->assign([
                'crud' => [
                    'context'    => 'create',
                    'properties' => $properties,
                    'model'      => $this->extractModuleModelName(),
                    'actions'    => $this->crudActions,
                    'item'       => [],
                ],
            ]);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function viewAction(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();

            $entity = $this->crudService->list($this->model, $params);
            Oforge()->View()->assign([
                'item' => $entity,
            ]);
            //TODO
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function updateAction(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();

            // $entity = $this->crudService->list($this->model, $params);
            // Oforge()->View()->assign([
            //     'item' => $entity,
            // ]);
            //TODO
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function deleteAction(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();

            // $entity = $this->crudService->list($this->model, $params);
            // Oforge()->View()->assign([
            //     'item' => $entity,
            // ]);
            //TODO
        }
    }

    /**
     * @inheritdoc
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        if ($this->crudActions['index'] ?? true) {
            $this->ensurePermissions('indexAction', BackendUser::class, $this->crudPermission);
        }
        if ($this->crudActions['create'] ?? true) {
            $this->ensurePermissions('createAction', BackendUser::class, $this->crudPermission);
        }
        if ($this->crudActions['view'] ?? true) {
            $this->ensurePermissions('viewAction', BackendUser::class, $this->crudPermission);
        }
        if ($this->crudActions['update'] ?? true) {
            $this->ensurePermissions('updateAction', BackendUser::class, $this->crudPermission);
        }
        if ($this->crudActions['delete'] ?? true) {
            $this->ensurePermissions('deleteAction', BackendUser::class, $this->crudPermission);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    protected function index(Request $request, Response $response) {
        $params   = $request->getQueryParams();
        $entities = $this->crudService->list($this->model, $params);
        list($properties, $hasEditors) = $this->filterPropertiesFor('index');

        // $entities[1]['name'] = '#ccc';
        // $entities[1]['name'] = ['test' => 'xxx'];

        Oforge()->View()->assign([
            'crud' => [
                'context'       => 'index',
                'properties'    => $properties,
                'model'         => $this->extractModuleModelName(),
                'actions'       => $this->crudActions,
                'hasEditors'    => $hasEditors,
                'hasRowActions' => $this->hasIndexRowActions(),
                'items'         => $entities,
            ],
        ]);
        // Oforge\Engine\Modules\I18n\Models\Snippet;
        // preg_match($pattern, $subject)
        // echo substr($this->model, 'Modules')
    }

    /**
     * @param string $crudAction
     *
     * @return array
     */
    protected function filterPropertiesFor(string $crudAction) {
        $hasEditors = false;
        $properties = [];
        if (isset($this->modelProperties)) {
            foreach ($this->modelProperties as $property) {
                if (isset($property['crud'][$crudAction])) {
                    if ($property['crud'][$crudAction] === 'off') {
                        continue;
                    } elseif ($property['crud'][$crudAction] === 'editable') {
                        $hasEditors = true;
                    }
                    if (isset($property['list']) && is_string($property['list']) && method_exists($this, $property['list'])) {
                        $property['list'] = $this->{$property['list']}();
                    }
                    $properties[] = $property;
                }
            }
        }

        return [$properties, $hasEditors];
    }

    /**
     * @return string
     */
    protected function extractModuleModelName() {
        $parts = explode('\\Models\\', $this->model, 2);
        if (count($parts) === 2) {
            $module    = substr($parts[0], 1 + strrpos($parts[0], '\\'));
            $modelName = $parts[1];

            return $module . $modelName;
        }

        return '';
    }

    /**
     * @return bool
     */
    protected function hasIndexRowActions() {
        if (isset($this->crudActions)) {
            $actionKeys = ['view', 'update', 'delete'];
            foreach ($actionKeys as $actionKey) {
                if (isset($this->crudActions[$actionKey]) && $this->crudActions[$actionKey]) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     */
    protected function delete(Request $request, Response $response) {
        if (isset($model)) {
            $params = $request->getParams();
            if (isset($params['id'])) {
                $this->crudService->delete($this->model, $params['id']);

                Oforge()->View()->assign([
                    'message' => [
                        'type'     => 'danger',
                        'body'     => 'backend_message_delete_success_body',
                        'headline' => 'backend_message_delete_success_headline',
                    ],
                ]);
            }
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     */
    protected function update(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();

            $this->crudService->update($this->model, $params);
            Oforge()->View()->assign([
                'message' => [
                    'type'     => 'danger',
                    'body'     => 'backend_message_update_success_body',
                    'headline' => 'backend_message_update_success_headline',
                ],
            ]);
        }
    }
}


