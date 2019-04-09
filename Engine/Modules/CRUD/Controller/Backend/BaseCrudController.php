<?php

namespace Oforge\Engine\Modules\CRUD\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CrudController
 *
 * @package Oforge\Engine\Modules\CRUD\Controller\Backend
 */
class BaseCrudController extends SecureBackendController {
    /** @var string $model */
    protected $model = null;
    /**
     * Define model properties (editor, visibility in index table and crate/update forms) e.g.
     *      protected $modelProperties = [
     *          [
     *              'name'      => 'id',    // Property name. Required
     *              'label'     => 'backend_crud_property__<Module><ModelName>_<propertyName>', //own i18n key
     *              'type'      => CrudDataTypes::..., // Required
     *              'crud'      => [        // default = off (not rendered). (Required)
     *                  'index'     => 'off|readonly|editable',
     *                  'view'      => 'off|readonly|editable',
     *                  'create'    => 'off|readonly|editable',
     *                  'update'    => 'off|readonly|editable',
     *                  'delete'    => 'off|readonly|editable',
     *              ],
     *              'list'          => [ // If type = select. Name of a protected function to create a dynamic array (e.g. 'getListUsers', or a static array. (Required)
     *                  <value> => <option text or label e.g. 'backend_crud_property__<name>_<value>'>  // value => (text|(i18n-)label) pair
     *              ],
     *                  'listI18nLabel' => true,    // If type = select. Is Select label i18n-label? (Optional)
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
     *          'index'     => true, // enable view
     *          'create'    => true, // enable create button and view
     *          'view'      => true, // enable update button and view
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
    /** @var string $moduleModelName */
    private $moduleModelName;

    /**
     * CrudController constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        $this->crudService     = Oforge()->Services()->get('crud');
        $this->moduleModelName = '';
        if (isset($this->model)) {
            $parts = explode('\\Models\\', $this->model, 2);
            if (count($parts) === 2) {
                $module                = substr($parts[0], 1 + strrpos($parts[0], '\\'));
                $modelName             = $parts[1];
                $this->moduleModelName = $module . '_' . $modelName;
            }
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        if (!isset($this->model)) {
            return;
        }
        $params = $request->getParams();
        if ($request->isPost() && !empty($params)) {
            if (isset($params['action'])) {
                if ($params['action'] === 'update') {
                    $this->indexUpdate($request, $response);
                }
            }
        }
        $params   = $request->getQueryParams();
        $entities = $this->crudService->list($this->model, $params);
        list($properties, $hasEditors) = $this->filterPropertiesFor('index');
        $hasRowActions = false;
        if (isset($this->crudActions)) {
            $actionKeys = ['view', 'update', 'delete'];
            foreach ($actionKeys as $actionKey) {
                if (isset($this->crudActions[$actionKey]) && $this->crudActions[$actionKey]) {
                    $hasRowActions = true;
                    break;
                }
            }
        }

        Oforge()->View()->assign([
            'crud' => [
                'context'       => 'index',
                'properties'    => $properties,
                'model'         => $this->moduleModelName,
                'actions'       => $this->crudActions,
                'hasEditors'    => $hasEditors,
                'hasRowActions' => $hasRowActions,
                'items'         => $entities,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function createAction(Request $request, Response $response) {
        if (!isset($this->model)) {
            return;
        }
        $params = $request->getParams();
        if ($request->isPost() && !empty($params)) {
            try {
                $this->crudService->create($this->model, $params['data']);
                // TODO flash message
                // $this->viewAssignMessage('success', 'backend_message_create_success_body', 'backend_message_create_success_headline');
            } catch (Exception $exception) {
                // TODO flash message
                // $this->viewAssignMessage('danger', 'backend_message_create_error_body', 'backend_message_create_error_headline', $exception);
            }
        }
        list($properties, $hasEditors) = $this->filterPropertiesFor('create');
        Oforge()->View()->assign([
            'crud' => [
                'context'    => 'create',
                'properties' => $properties,
                'model'      => $this->moduleModelName,
                'actions'    => $this->crudActions,
                'item'       => [],
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function viewAction(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();

            $entity = $this->crudService->getById($this->model, $params['id']);
            list($properties, $hasEditors) = $this->filterPropertiesFor('view');
            Oforge()->View()->assign([
                'crud' => [
                    'context'    => 'view',
                    'properties' => $properties,
                    'model'      => $this->moduleModelName,
                    'actions'    => $this->crudActions,
                    'item'       => $entity,
                ],
            ]);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function updateAction(Request $request, Response $response) {
        if (!isset($this->model)) {
            return;
        }
        $params = $request->getParams();
        if ($request->isPost() && !empty($params)) {
            try {
                $data = $this->convertData($params['data']);
                $this->crudService->update($this->model, $data);
                // TODO flash message
                // $this->viewAssignMessage('success', 'backend_message_update_success_body', 'backend_message_update_success_headline');
            } catch (Exception $exception) {
                // TODO flash message
                // $this->viewAssignMessage('danger', 'backend_message_update_error_body', 'backend_message_update_error_headline', $exception);
            }
        }
        $entity = $this->crudService->getById($this->model, $params['id']);
        list($properties, $hasEditors) = $this->filterPropertiesFor('update');
        Oforge()->View()->assign([
            'crud' => [
                'context'    => 'update',
                'properties' => $properties,
                'model'      => $this->moduleModelName,
                'actions'    => $this->crudActions,
                'item'       => $entity,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     */
    public function deleteAction(Request $request, Response $response) {
        if (!isset($this->model)) {
            return;
        }
        $deleted = false;
        $params  = $request->getParams();
        if ($request->isPost() && !empty($params)) {
            try {
                $this->crudService->delete($this->model, $params['id']);
                // TODO flash message
                // $this->viewAssignMessage('success', 'backend_message_delete_success_body', 'backend_message_delete_success_headline');
                $deleted = true;
            } catch (Exception $exception) {
                // TODO flash message
                // $this->viewAssignMessage('danger', 'backend_message_delete_error_body', 'backend_message_delete_error_headline', $exception);
            }
        }
        if ($deleted) {
            Oforge()->View()->assign([
                'crud' => [
                    'context' => 'delete',
                    'deleted' => true,
                ],
            ]);
        } else {
            $entity = $this->crudService->getById($this->model, $params['id']);
            list($properties, $hasEditors) = $this->filterPropertiesFor('delete');
            Oforge()->View()->assign([
                'crud' => [
                    'context'    => 'delete',
                    'properties' => $properties,
                    'model'      => $this->moduleModelName,
                    'actions'    => $this->crudActions,
                    'item'       => $entity,
                ],
            ]);
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
     * Convert form data to model data, e.g. string to DateTime.
     *
     * @param array $data
     *
     * @return array
     */
    protected function convertData(array $data) : array {
        return $data;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotFoundException
     */
    protected function indexUpdate(Request $request, Response $response) {
        if (isset($this->model)) {
            $params = $request->getParams();
            $list   = $params['data'];
            foreach ($list as $index => $data) {
                $list[$index] = $this->convertData($data);
            }
            $params['data'] = $list;
            $this->crudService->update($this->model, $params);
            // TODO flash message
            // Oforge()->View()->addFlashMessage('success', $message);
            // Oforge()->View()->assign([
            //     'message' => [
            //         'type'     => 'danger',
            //         'body'     => 'backend_message_update_success_body',
            //         'headline' => 'backend_message_update_success_headline',
            //     ],
            // ]);
        }
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

}


