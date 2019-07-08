<?php

namespace Oforge\Engine\Modules\AdminBackend\Cronjob\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Cronjob\Models\AbstractCronjob;
use Oforge\Engine\Modules\Cronjob\Services\CronjobService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class CronjobController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Cronjob\Controller\Backend
 * @EndpointClass(path="/backend/cronjob", name="backend_cronjob", assetScope="Backend")
 */
class CronjobController extends BaseCrudController {
    /** @var string $model */
    protected $model = AbstractCronjob::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'title',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'module_cronjob_property_title', 'default' => 'Title'],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'executionInterval',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'module_cronjob_property_execution_interval', 'default' => 'Execution interval'],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'lastExecutionTime',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'module_cronjob_property_last_execution', 'default' => 'Last execution'],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'  => 'nextExecutionTime',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'module_cronjob_property_next_execution', 'default' => 'Next execution'],
            'crud'  => [
                'index' => 'readonly',
            ],
        ],
        [
            'name'     => 'execute',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'module_cronjob_button_execute', 'default' => 'Execution'],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/Cronjob/Index/Action/Execute.twig',
            ],
        ],
    ];

    /** @var array $crudActions */
    protected $crudActions = [
        'index'  => true,
        'create' => false,
        'view'   => false,
        'update' => false,
        'delete' => false,
    ];

    public function __construct() {
        parent::__construct();
    }

    /** @EndpointAction(create=false) */
    public function createAction(Request $request, Response $response) {
    }

    /** @EndpointAction(create=false) */
    public function updateAction(Request $request, Response $response, array $args) {
    }

    /** @EndpointAction(create=false) */
    public function deleteAction(Request $request, Response $response, array $args) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/run/{name}")
     */
    public function runAction(Request $request, Response $response, array $args) {
        /** @var CronjobService $cronjobService */
        $cronjobService = Oforge()->Services()->get('cronjob');
        $cronjobService->run($args['name']);

        return RouteHelper::redirect($response, 'backend_cronjob');
    }
}
