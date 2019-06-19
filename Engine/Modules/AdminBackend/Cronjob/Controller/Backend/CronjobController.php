<?php

namespace Oforge\Engine\Modules\AdminBackend\Cronjob\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Cronjob\Models\AbstractCronjob;
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
            'name' => 'title',
            'type' => CrudDataTypes::STRING,
            'crud' => [
                'index' => 'readonly',
            ],
        ],
        [
            'name' => 'executionInterval',
            'type' => CrudDataTypes::STRING,
            'crud' => [
                'index' => 'readonly',
            ],
        ],
        [
            'name' => 'lastExecutionTime',
            'type' => CrudDataTypes::STRING, // TODO Date Type
            'crud' => [
                'index' => 'readonly',
            ],
        ],
        [
            'name' => 'nextExecutionTime',
            'type' => CrudDataTypes::STRING, // TODO Date Type
            'crud' => [
                'index' => 'readonly',
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

}
