<?php

namespace Oforge\Engine\Modules\CRUD\Controller\Backend;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Helper\StringHelper;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Media\Models\Media;
use Oforge\Engine\Modules\Media\Services\MediaService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BaseCrudController
 *
 * @package Oforge\Engine\Modules\CRUD\Controller\Backend
 */
class BaseCrudController extends SecureBackendController
{
    /** @var string $model */
    protected $model = null;
    /**
     * Define model properties (editor, visibility in index table and crate/update forms) e.g.
     *      protected $modelProperties = [
     *          [
     *              // Property name. Required
     *              'name'      => 'propertyName',
     *               // Text label or i18n array with key and default value.
     *              'label'     => 'i18n-translated-text' | ['key' => 'label_id', 'default' => 'ID'],
     *              'type'      => CrudDataTypes::..., // Required
     *              'crud'      => [    // default = off (not rendered, if missing off). (Required)
     *                  'index'     => 'off|readonly|editable',
     *                  'view'      => 'off|readonly|editable',
     *                  'create'    => 'off|readonly|editable',
     *                  'update'    => 'off|readonly|editable',
     *                  'delete'    => 'off|readonly|editable',
     *              ],
     *              // Select with optgroups for type=select
     *              'list' => 'functionName' | [
     *                  'key'   => [
     *                      'label'     => 'i18n-translated-text',
     *                      'options'   => [
     *                          'value' => => 'i18n-translated-text',
     *                      ]
     *                  ],
     *              ],
     *              // Simple select for type=select or optional as datalist for type=string
     *              'list' => 'functionName' | [
     *                  'value' => 'i18n-translated-text', # Simple select
     *              ],
     *              'multiple' => false,   // If type = select. (Optional)
     *              'editor' => [       // Configuration for field editor.
     *                  'hint'          => 'i18n-translated-text'| ['key' => 'label_id', 'default' => 'ID'],     // Hint text (in index colum header and under editor field).(Optional)
     *                  'default'       => '',      // Default value. (Optional)
     *                  'custom'        => '...'    // If type = custom. Twig path for include.
     *                  'required'      => false,   // (Optional)
     *                  'pattern'       => '...',   // If type = string. (Optional)
     *                  'placeholder'   => '...',   // i18n-translated-text. (Optional)
     *                  'maxlength'     => ...,     // If type = string|text. (Optional)
     *                  'min'           => '...',   // If type = int|float|currency. (Optional)
     *                  'max"           => ...,     // If type = string|text. (Optional)
     *                  'step"          => ...,     // If type = string|text. (Optional)
     *                  'size'          => ...,     // If type = select. (Optional)
     *                  'queryAutoFill' => true,   // Auto fill/select value on create by query parameter (Optional)
     *              ],
     *              'renderer' => [ // Configuration for renderer
     *                  'alignment' => 'left|center|right', // If type = int|float|currency then default = right otherwise left. (Optional)
     *                  'custom'    => '...',   // If type = custom. Twig path for include.
     *                  'width'     => 200,     // if type = image (Optional)
     *              ],
     *              'colorsPreview' => [
     *                  'text'          => 'textPropertyName',              // Name of listening text property (Optional)
     *                  'background'    => 'backgroundColorPropertyName',   // Name of listening background color property (Optional)
     *                  'foreground'    => 'foregroundColorPropertyName',   // Name of listening foreground color property (Optional)
     *              ],
     *          ], ...
     *      ];
     *
     * @var array|null $modelProperties
     */
    protected $modelProperties = null;
    /**
     * Enable or disable crud actions (missing = false) for this model with
     *      protected $crudActions = [
     *          'index'     => true, // enable view
     *          'create'    => true, // enable create button and view
     *          'view'      => true, // enable update button (visible if update false) and view
     *          'update'    => true, // enable update button and view
     *          'delete'    => true, // enable delete button and view
     *      ];
     *
     * @var array $crudActions
     */
    protected $crudActions = [
        'index'  => true,
        'create' => true,
        'view'   => true,
        'update' => true,
        'delete' => true,
    ];
    /** @var int|array<string,int> $crudPermission */
    protected $crudPermissions = [
        'index'  => BackendUser::ROLE_MODERATOR,
        'create' => BackendUser::ROLE_MODERATOR,
        'view'   => BackendUser::ROLE_MODERATOR,
        'update' => BackendUser::ROLE_MODERATOR,
        'delete' => BackendUser::ROLE_MODERATOR,
    ];
    /**
     * Configuration of the filters on the index view.
     *      protected $indexFilter = [
     *          'propertyName' => [
     *              'type'              => CrudFilterType::...,
     *              'label'             => 'Text' | ['key' => 'i18nLabel', 'default' => 'DefaultText'],
     *              'compare'           => CrudFilterComparator::...,    #Default = equals
     *              'list'              => '',   # Required list for type=select or optional datalist for type=text, array or protected function name.
     *              'customFilterQuery' => callable|'<ThisClassMethodName>', #Callable or method name (of this object).
     *                  # Parameters (\Doctrine\ORM\QueryBuilder $queryBuilder, array $queryValues),
     *                  # the queryValues parameter contains only existing and not empty query values.
     *                  # If this key is contained in one of the filters configs, the filtering must be written completely (also for all other properties).
     *                  # Only the first filter callable will be used, all others are ignored.
     *          ],
     *      ];
     *
     * @var array $indexFilter
     */
    protected $indexFilter = [];
    /**
     * Configuration of the orderBy on the index view.
     *      protected $indexOrderBy = [
     *          'propertyName' => CrudGroupByOrder::ASC|DESC,
     *      ];
     *
     * @var array $indexOrderBy
     */
    protected $indexOrderBy = [];
    /**
     * Configuration of the orderBy query keys on the index view.
     *      protected $indexOrderByQueryKeys = [
     *          'orderBy'         => 'orderBy',
     *          'order'           => 'order',
     *          'page'            => 'page',
     *          'entitiesPerPage' => 'entitiesPerPage',
     *      ];
     *
     * @var array $indexOrderBy
     */
    protected $indexReservedQueryKeys = [
        'orderBy'         => 'orderBy',
        'order'           => 'order',
        'page'            => 'page',
        'entitiesPerPage' => 'entitiesPerPage',
    ];
    /**
     * Configuration of the pagination on the index view. Disable with null or override with
     *      protected $indexPagination = [
     *          'default' => 25,
     *          'buttons' => [25, 50, 75, 100, 250],
     *         'queryKeys' => [
     *              'page'            => 'p',
     *              'entitiesPerPage' => 'epp',
     *          ],
     *      ];
     *
     * @var array $indexPagination
     */
    protected $indexPagination = [
        'default' => 10,
        'buttons' => [10, 25, 50, 100, 250],
    ];
    /** @var GenericCrudService $crudService */
    protected $crudService;
    /** @var string $moduleModelName */
    protected $moduleModelName;
    /** @var array $filterSelectData */
    protected $filterSelectData = [];

    /**
     * BaseCrudController constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct()
    {
        if (is_null($this->model) || is_null($this->modelProperties)) {
            echo 'Properties "$model" and "$modelProperties" must be override!';
            die();
        }
        $this->crudService     = Oforge()->Services()->get('crud');
        $this->moduleModelName = '';
        if (isset($this->model)) {
            $parts = explode('\\Models\\', $this->model, 2);
            if (count($parts) === 2) {
                $module    = substr($parts[0], strrpos($parts[0], '\\'));
                $module    = StringHelper::leftTrim($module, '\\');
                $modelName = $parts[1];
                if (strpos($modelName, '\\')) {
                    $modelName = substr($modelName, 1 + strrpos($modelName, '\\'));
                }
                $this->moduleModelName = $module . '_' . $modelName;
            }
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response)
    {
        $queryParams = $request->getQueryParams();
        $postData    = $request->getParsedBody();
        if ($request->isPost() && !empty($postData)) {
            if (isset($postData['action'])) {
                $methodName = 'handleIndex' . ucfirst($postData['action']);
                if (method_exists($this, $methodName)) {
                    $this->{$methodName}($postData);
                }

                return $this->redirect($response, 'index', [], $queryParams);
            }
        }
        unset($postData);
        $pagination = $this->prepareIndexPaginationData($queryParams);
        $orderBy    = $this->evaluateIndexOrder($queryParams);
        $criteria   = $this->evaluateIndexFilter($queryParams);

        $entities = $this->crudService->list($this->model, $criteria, $orderBy, $pagination['offset'], $pagination['limit']);
        if (Oforge()->View()->Flash()->hasData($this->moduleModelName)) {
            $postData = Oforge()->View()->Flash()->getData($this->moduleModelName);
            Oforge()->View()->Flash()->clearData($this->moduleModelName);
        }
        if ( !empty($entities)) {
            foreach ($entities as $index => $entity) {
                $entity = $this->prepareItemDataArray($entity, 'index');
                if (isset($postData[$index])) {
                    $entity = ArrayHelper::mergeRecursive($entity, $postData[$index]);
                }
                $entities[$index] = $entity;
            }
        }
        [$properties, $hasEditors] = $this->filterPropertiesFor('index');
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
        $filters = $this->indexFilter;
        foreach ($filters as $propertyName => &$filter) {
            $this->processSelectListCallable($filter, $propertyName);
        }
        unset($filter);

        Oforge()->View()->assign(
            [
                'crud' => [
                    'context'       => 'index',
                    'properties'    => $properties,
                    'model'         => $this->moduleModelName,
                    'actions'       => $this->crudActions,
                    'hasEditors'    => $hasEditors,
                    'hasRowActions' => $hasRowActions,
                    'items'         => $entities,
                    'pagination'    => $pagination,
                    'queryKeys'     => $this->indexReservedQueryKeys,
                    'filter'        => $filters,
                ],
            ]#
        );

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function createAction(Request $request, Response $response)
    {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData)) {
            try {
                $data = $postData['data'];
                $this->handleFileUploads($data, 'create');
                $data = $this->convertData($data, 'create');
                $this->crudService->create($this->model, $data);
                Oforge()->View()->Flash()->addMessage(
                    'success',
                    I18N::translate(
                        'backend_crud_msg_create_success',
                        [
                            'en' => 'Entity successfully created.',
                            'de' => 'Das Element wurde erfolgreich erstellt.',
                        ]#
                    )
                );

                return $this->redirect($response, 'index');
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addExceptionMessage(
                    'error',
                    I18N::translate(
                        'backend_crud_msg_create_failed',
                        [
                            'en' => 'Entity creation failed.',
                            'de' => 'Erstellung des Elements fehlgeschlagen.',
                        ]#
                    ),
                    $exception
                );
                Oforge()->View()->Flash()->setData($this->moduleModelName, $data);

                return $this->redirect($response, 'create');
            }
        }
        $entity = $this->prepareItemDataArray(null, 'create', $request->getQueryParams());
        if (Oforge()->View()->Flash()->hasData($this->moduleModelName)) {
            $postData = Oforge()->View()->Flash()->getData($this->moduleModelName);
            $entity   = ArrayHelper::mergeRecursive($entity, $postData);
            Oforge()->View()->Flash()->clearData($this->moduleModelName);
        }
        [$properties, $hasEditors] = $this->filterPropertiesFor('create');
        Oforge()->View()->assign(
            [
                'crud' => [
                    'context'    => 'create',
                    'properties' => $properties,
                    'model'      => $this->moduleModelName,
                    'actions'    => $this->crudActions,
                    'item'       => $entity,
                ],
            ]#
        );

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/view/{id}")
     */
    public function viewAction(Request $request, Response $response, array $args)
    {
        $entity = $this->crudService->getById($this->model, $args['id']);
        $entity = $this->prepareItemDataArray($entity, 'view');
        [$properties, $hasEditors] = $this->filterPropertiesFor('view');
        Oforge()->View()->assign(
            [
                'crud' => [
                    'context'    => 'view',
                    'properties' => $properties,
                    'model'      => $this->moduleModelName,
                    'actions'    => $this->crudActions,
                    'item'       => $entity,
                ],
            ]#
        );

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/update/{id}")
     */
    public function updateAction(Request $request, Response $response, array $args)
    {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData)) {
            try {
                $data = $postData['data'];
                $this->handleFileUploads($data, 'update');
                $data = $this->convertData($data, 'update');
                $this->crudService->update($this->model, $data);
                Oforge()->View()->Flash()->addMessage(
                    'success',
                    I18N::translate(
                        'backend_crud_msg_update_success',
                        [
                            'en' => 'Entity successfully updated.',
                            'de' => 'Element erfolgreich aktualisiert.',
                        ]#
                    )
                );
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addExceptionMessage(
                    'error',
                    I18N::translate(
                        'backend_crud_msg_update_failed',
                        [
                            'en' => 'Entity update failed.',
                            'de' => 'Aktualisieren des Elements fehlgeschlagen.',
                        ]#
                    ),
                    $exception
                );
                Oforge()->View()->Flash()->setData($this->moduleModelName, $data);
            }

            return $this->redirect($response, 'update', ['id' => $args['id']]);
        }
        $entity = $this->crudService->getById($this->model, $args['id']);
        $entity = $this->prepareItemDataArray($entity, 'update');
        if (Oforge()->View()->Flash()->hasData($this->moduleModelName)) {
            $postData = Oforge()->View()->Flash()->getData($this->moduleModelName);
            $entity   = ArrayHelper::mergeRecursive($entity, $postData);
            Oforge()->View()->Flash()->clearData($this->moduleModelName);
        }
        [$properties, $hasEditors] = $this->filterPropertiesFor('update');
        Oforge()->View()->assign(
            [
                'crud' => [
                    'context'    => 'update',
                    'properties' => $properties,
                    'model'      => $this->moduleModelName,
                    'actions'    => $this->crudActions,
                    'item'       => $entity,
                ],
            ]#
        );

        return $response;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/delete/{id}")
     */
    public function deleteAction(Request $request, Response $response, array $args)
    {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData)) {
            $return = $this->handleDeleteAction($response, $args['id']);
            if (isset($return)) {
                return $return;
            }
        }
        $entity = $this->crudService->getById($this->model, $args['id']);
        $entity = $this->prepareItemDataArray($entity, 'delete');
        [$properties, $hasEditors] = $this->filterPropertiesFor('delete');
        Oforge()->View()->assign(
            [
                'crud' => [
                    'context'    => 'delete',
                    'properties' => $properties,
                    'model'      => $this->moduleModelName,
                    'actions'    => $this->crudActions,
                    'item'       => $entity,
                ],
            ]#
        );

        return $response;
    }

    /** @inheritdoc */
    public function initPermissions()
    {
        $actions         = ['index', 'create', 'view', 'update', 'delete',];
        $crudActions     = $this->crudActions;
        $crudPermissions = $this->crudPermissions;
        foreach ($actions as $action) {
            if (ArrayHelper::get($crudActions, $action, true)) {
                $actionPermission = is_array($crudPermissions) ? ArrayHelper::get($crudPermissions, $action, BackendUser::ROLE_MODERATOR) : $crudPermissions;
                $this->ensurePermission($action . 'Action', $actionPermission);
            }
        }
    }

    /**
     * Process select list callable.
     *
     * @param array $data
     * @param string $propertyName
     */
    protected function processSelectListCallable(array &$data, string $propertyName)
    {
        if (isset($data['list']) && is_string($data['list']) && method_exists($this, $data['list'])) {
            if ( !isset($this->filterSelectData[$propertyName])) {
                $this->filterSelectData[$propertyName] = $this->{$data['list']}();
            }
            $data['list'] = $this->filterSelectData[$propertyName];
        }
    }

    /**
     * Convert form data to model data, e.g. string to DateTime.
     *
     * @param array $data
     * @param string $crudAction
     *
     * @return array
     * @throws Exception
     */
    protected function convertData(array $data, string $crudAction) : array
    {
        return $data;
    }

    /**
     * Prepare Item data for view, e.G. DateTime to string or custom column data.
     *
     * @param AbstractModel|null $entity
     * @param string $crudAction
     * @param array $queryParams Usage for create action
     *
     * @return array
     */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction, array $queryParams = []) : array
    {
        $data = isset($entity) ? $entity->toArray() : [];
        if ($crudAction === 'create') {
            [$properties, $hasEditors] = $this->filterPropertiesFor('create');
            foreach ($properties as $property) {
                $name = $property['name'];
                if (isset($queryParams[$name]) && ArrayHelper::get($property['editor'] ?? [], 'queryAutoFill', true)) {
                    $data[$name] = $queryParams[$name];
                } elseif (isset($property['editor']['default'])) {
                    $data[$name] = $property['editor']['default'];
                }
            }
        }

        return $data;
    }

    /**
     * @param Response $response
     * @param string $entityID
     *
     * @return Response
     */
    protected function handleDeleteAction(Response $response, string $entityID)
    {
        try {
            $this->crudService->delete($this->model, $entityID);
            Oforge()->View()->Flash()->addMessage(
                'success',
                I18N::translate(
                    'backend_crud_msg_delete_success',
                    [
                        'en' => 'Entity successfully delete.',
                        'de' => 'Element erfolgreich gelöscht.',
                    ]#
                )
            );

            return $this->redirect($response, 'index');
        } catch (Exception $exception) {
            Oforge()->View()->Flash()->addExceptionMessage(
                'error',
                I18N::translate(
                    'backend_crud_msg_delete_failed',
                    [
                        'en' => 'Entity deletion failed.',
                        'de' => 'Löschen des Elements fehlgeschlagen.',
                    ]#
                ),
                $exception
            );

            return $this->redirect($response, 'delete', ['id' => $entityID]);
        }
    }

    /**
     * Handle update action on crud index.
     *
     * @param array $postData
     */
    protected function handleIndexUpdate(array $postData)
    {
        $list = $postData['data'];
        $this->handleFileUploads($list, 'index');
        try {
            foreach ($list as $entityID => $data) {
                $list[$entityID] = $this->convertData($data, 'update');
            }
            $postData['data'] = $list;
            $this->crudService->update($this->model, $postData);
            Oforge()->View()->Flash()->addMessage(
                'success',
                I18N::translate(
                    'backend_crud_msg_bulk_update_success',
                    [
                        'en' => 'Entities successfully bulk updated.',
                        'de' => 'Alle Elemente wurden erfolgreich aktualisiert.',
                    ]#
                )
            );
        } catch (Exception $exception) {
            Oforge()->View()->Flash()->addExceptionMessage(
                'error',
                I18N::translate(
                    'backend_crud_msg_bulk_update_failed',
                    [
                        'en' => 'Entities bulk update failed.',
                        'de' => 'Aktualisierung der Elemente fehlgeschlagen.',
                    ]#
                ),
                $exception
            );
            Oforge()->View()->Flash()->setData($this->moduleModelName, $postData['data']);
        }
    }

    /**
     * Filter properties based on crud action for view.
     *
     * @param string $crudAction
     *
     * @return array
     */
    protected function filterPropertiesFor(string $crudAction) : array
    {
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
                    $this->processSelectListCallable($property, $property['name']);
                    $propertyName = $property['name'];

                    $properties[$propertyName] = $this->modifyPropertyConfig($property, $crudAction);
                }
            }
        }

        return [$properties, $hasEditors];
    }

    /**
     * Modifying of property config at runtime, e.g. for adding options by configs.
     *
     * @param array $config
     * @param string $crudAction
     *
     * @return array
     */
    protected function modifyPropertyConfig(array $config, string $crudAction)
    {
        return $config;
    }

    /**
     * Redirect/reload after post request.
     *
     * @param Response $response
     * @param string $crudAction
     * @param array $urlParams
     * @param array $queryParams
     *
     * @return Response
     */
    protected function redirect(Response $response, string $crudAction, array $urlParams = [], array $queryParams = [])
    {
        $routeName = Oforge()->View()->get('meta')['route']['parentName'];
        if ($crudAction !== 'index') {
            $routeName .= '_' . $crudAction;
        }

        return RouteHelper::redirect($response, $routeName, $urlParams, $queryParams);
    }

    /**
     * Evaluates query filter params.
     *
     * @param array $queryParams
     *
     * @return array|callable
     */
    protected function evaluateIndexFilter(array $queryParams)
    {
        $queryKeys               = $this->indexReservedQueryKeys;
        $queryKeyPage            = $queryKeys['page'];
        $queryKeyEntitiesPerPage = $queryKeys['entitiesPerPage'];
        unset($queryParams[$queryKeyPage], $queryParams[$queryKeyEntitiesPerPage]);

        $customFilterCallable    = null;
        $customFilterQueryValues = [];
        $filters                 = [];

        if ( !empty($this->indexFilter)) {
            foreach ($this->indexFilter as $propertyName => $filterConfig) {
                $propertyNameValue = null;
                if (isset($queryParams[$propertyName]) && $queryParams[$propertyName] !== '') {
                    $propertyNameValue                      = $queryParams[$propertyName];
                    $customFilterQueryValues[$propertyName] = $propertyNameValue;
                }
                if (isset($filterConfig['customFilterQuery']) && $customFilterCallable === null) {
                    if (isset($filterConfig['customFilterQuery'])) {
                        $callable = $filterConfig['customFilterQuery'];
                        if (is_callable($callable)) {
                            $customFilterCallable = $callable;
                        } elseif (is_string($callable) && method_exists($this, $callable)) {
                            $customFilterCallable = [$this, $callable];
                        }
                    }
                } elseif ($propertyNameValue !== null) {
                    switch ($filterConfig['type']) {
                        case CrudFilterType::SELECT:
                            $comparator = CrudFilterComparator::EQUALS;
                            break;
                        case CrudFilterType::TEXT:
                        case CrudFilterType::HIDDEN:
                            $comparator = ArrayHelper::get($filterConfig, 'compare', CrudFilterComparator::EQUALS);
                            break;
                        default:
                            continue 2;
                    }
                    switch ($comparator) {
                        case CrudFilterComparator::EQUALS:
                        case CrudFilterComparator::NOT_EQUALS:
                        case CrudFilterComparator::LIKE:
                        case CrudFilterComparator::NOT_LIKE:
                        case CrudFilterComparator::GREATER:
                        case CrudFilterComparator::GREATER_EQUALS:
                        case CrudFilterComparator::LESS:
                        case CrudFilterComparator::LESS_EQUALS:
                            break;
                        default:
                            $comparator = CrudFilterComparator::EQUALS;
                    }
                    $filters[$propertyName] = [
                        'comparator' => $comparator,
                        'value'      => $propertyNameValue,
                    ];
                }
            }

            if ($customFilterCallable !== null) {
                $filters = function (QueryBuilder $queryBuilder) use ($customFilterCallable, $customFilterQueryValues) {
                    $customFilterCallable($queryBuilder, $customFilterQueryValues);
                };
            }
        }

        return $filters;
    }

    /**
     * Evaluates query orderBy params.
     *
     * @param array|null $queryParams Return null if no ordering.
     *
     * @return array
     */
    protected function evaluateIndexOrder(array &$queryParams) : ?array
    {
        $queryKeys       = $this->indexReservedQueryKeys;
        $queryKeyOrderBy = $queryKeys['orderBy'];
        $queryKeyOrder   = $queryKeys['order'];
        $orderBy         = null;

        $propertyName = ArrayHelper::get($queryParams, $queryKeyOrderBy);
        $order        = ArrayHelper::get($queryParams, $queryKeyOrder);
        unset($queryParams[$queryKeyOrderBy], $queryParams[$queryKeyOrder]);
        if (isset($propertyName)) {
            if (isset($order)) {
                if ($order !== CrudGroupByOrder::ASC && $order !== CrudGroupByOrder::DESC) {
                    $order = CrudGroupByOrder::ASC;
                }
            }
            $orderBy = [
                $propertyName => $order,
            ];
        } elseif ( !empty($this->indexOrderBy)) {
            $orderBy = [];
            foreach ($this->indexOrderBy as $propertyName => $order) {
                if ($order !== CrudGroupByOrder::ASC && $order !== CrudGroupByOrder::DESC) {
                    $order = CrudGroupByOrder::ASC;
                }
                $orderBy[$propertyName] = $order;
            }
            if ( !empty($tmpOrderBy)) {
                $orderBy = $tmpOrderBy;
            }
        }

        return $orderBy;
    }

    /** Handles uploaded media files (find by defined modelProperties).
     *
     * @param array|null $postData
     * @param string $crudAction
     */
    protected function handleFileUploads(?array &$postData, string $crudAction)
    {
        if (empty($postData) || empty($this->modelProperties) || !isset($_FILES['data'])) {
            return;
        }
        $isSingle  = $crudAction !== 'index';
        $filesMeta = $_FILES['data'];

        foreach ($this->modelProperties as $property) {
            if ( !isset($property['type']) || !in_array($property['type'], [CrudDataTypes::IMAGE])) {
                continue;
            }
            $propertyName = $property['name'];
            if ($isSingle) {
                if ( !isset($postData[$propertyName]['action'])) {
                    continue;
                }
                $fileData = [
                    'entityID' => $postData['id'],
                    'action'   => $postData[$propertyName]['action'],
                    'new_name' => $postData[$propertyName]['filename'],
                    // 'mediaID'  => $postData[$propertyName]['mediaID'],
                ];
                if (isset($filesMeta['name'][$propertyName])) {
                    $fileData = array_merge(
                        $fileData,
                        [
                            'name'     => $filesMeta['name'][$propertyName],
                            'tmp_name' => $filesMeta['tmp_name'][$propertyName],
                            'type'     => $filesMeta['type'][$propertyName],
                            'error'    => $filesMeta['error'][$propertyName],
                            'size'     => $filesMeta['size'][$propertyName],
                        ]#
                    );
                }
                unset($postData[$propertyName]);
                $this->handleFileUpload($postData, $propertyName, $fileData);
            } else {
                foreach ($postData as $entityID => &$data) {
                    if ( !isset($data[$propertyName]['action'])) {
                        continue;
                    }
                    $fileData = [
                        'entityID' => $entityID,
                        'action'   => $data[$propertyName]['action'],
                        'new_name' => $data[$propertyName]['filename'],
                        // 'mediaID'  => $data[$propertyName]['mediaID'],
                    ];
                    if (isset($filesMeta['name'][$entityID][$propertyName])) {
                        $fileData = array_merge(
                            $fileData,
                            [
                                'name'     => $filesMeta['name'][$entityID][$propertyName],
                                'tmp_name' => $filesMeta['tmp_name'][$entityID][$propertyName],
                                'type'     => $filesMeta['type'][$entityID][$propertyName],
                                'error'    => $filesMeta['error'][$entityID][$propertyName],
                                'size'     => $filesMeta['size'][$entityID][$propertyName],
                            ]#
                        );
                    }
                    unset($data[$propertyName]);
                    $this->handleFileUpload($data, $propertyName, $fileData);
                }
                unset($data);
            }
        }
    }

    /**
     * /** Handle uploaded media file for single field.
     *
     * @param array $entityData
     * @param string $propertyName
     * @param array $fileData
     */
    protected function handleFileUpload(array &$entityData, string $propertyName, array $fileData)
    {
        /**
         * @var MediaService $mediaService
         * @var Media|null $media
         */
        if ($fileData['action'] === 'delete') {
            //TODO delete orphan media?
            $entityData[$propertyName] = null;
        } elseif ($fileData['action'] === 'upload') {
            if ( !empty($fileData['new_name'])) {
                $oldName          = $fileData['name'];
                $fileExtension    = substr($oldName, strrpos($oldName, '.'));
                $fileData['name'] = $fileData['new_name'] . $fileExtension;
            }
            try {
                $mediaService = Oforge()->Services()->get('media');
                $media        = $mediaService->add($fileData);
                if (isset($media)) {
                    $entityData[$propertyName] = $media->getId();
                } else {
                    Oforge()->View()->Flash()->addMessage(
                        'error',
                        I18N::translate(
                            'backend_crud_image_upload_failed',
                            [
                                'en' => 'Image upload failed.',
                                'de' => 'Hochladen des Bilds fehlgeschlagen.',
                            ]#
                        )
                    );
                }
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addMessage(
                    'error',
                    I18N::translate(
                        'backend_crud_image_upload_failed',
                        [
                            'en' => 'Image upload failed.',
                            'de' => 'Hochladen des Bilds fehlgeschlagen.',
                        ]#
                    )
                );
                Oforge()->Logger()->logException($exception);
            }
        } elseif ($fileData['action'] === 'chooser') {
            try {
                $mediaService = Oforge()->Services()->get('media');
                $mediaID      = $fileData['mediaID'];
                $media        = $mediaService->getById($mediaID);
                if (isset($media)) {
                    $entityData[$propertyName] = $media->getId();
                } else {
                    Oforge()->View()->Flash()->addMessage(
                        'error',
                        sprintf(#
                            I18N::translate(
                                'backend_crud_media_not_found',
                                [
                                    'en' => 'Media entity with ID "%s" not found.',
                                    'de' => 'Medienelement mit ID "%s" nicht gefunden.',
                                ]#
                            ),#
                            $mediaID#
                        )
                    );
                }
            } catch (Exception $exception) {
                Oforge()->View()->Flash()->addMessage(
                    'error',
                    I18N::translate(
                        'backend_crud_image_replace_failed',
                        [
                            'en' => 'Image replace failed.',
                            'de' => 'Bildersetzung fehlgeschlagen.',
                        ]#
                    )
                );
                Oforge()->Logger()->logException($exception);
            }
        }
    }

    /**
     * Prepare data for index pagination.
     *
     * @param array $queryParams
     *
     * @return  array
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    protected function prepareIndexPaginationData(array $queryParams) : array
    {
        $queryKeys = $this->indexReservedQueryKeys;;
        $queryKeyPage            = $queryKeys['page'];
        $queryKeyEntitiesPerPage = $queryKeys['entitiesPerPage'];

        $itemsCount       = $this->crudService->count($this->model);
        $offset           = null;
        $entitiesPerPage  = null;
        $buttons          = null;
        $paginatorCurrent = null;
        $paginatorMax     = null;

        if (isset($this->indexPagination)) {
            $buttons = $this->indexPagination['buttons'];
            if (isset($queryParams[$queryKeyEntitiesPerPage])) {
                $entitiesPerPage = $queryParams[$queryKeyEntitiesPerPage];
            } else {
                $entitiesPerPage = $this->indexPagination['default'];
            }
            $paginatorMax = ceil($itemsCount / $entitiesPerPage);
            if (isset($queryParams[$queryKeyPage])) {
                $paginatorCurrent = $queryParams[$queryKeyPage];
            } else {
                $paginatorCurrent = 1;
            }
            if ($paginatorCurrent > 1) {
                $offset = ($paginatorCurrent - 1) * $entitiesPerPage;
            }
        }

        return [
            'offset'  => $offset,
            'limit'   => $entitiesPerPage,
            'total'   => $itemsCount,
            'page'    => [
                'current' => $paginatorCurrent,
                'max'     => $paginatorMax,
            ],
            'buttons' => [
                'values'  => $buttons,
                'current' => $entitiesPerPage,
            ],
        ];
    }

}
