<?php

namespace Insertion\Controller\Backend;

use Insertion\Models\InsertionTypeGroup;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroubByOrder;

/**
 * Class CategoryController
 *
 * @package Insert\Controller\Backend\FrontendUserManagement
 * @EndpointClass(path="/backend/insertion/type/group", name="backend_insertion_insertion_type_group", assetScope="Backend")
 */
class BackendInsertionTypeGroupController extends BaseCrudController{
    /** @var string $model */
    protected $model = InsertionTypeGroup::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'id',
            'type'  => CrudDataTypes::INT,
            'label' => ['key' => 'plugin_insertion_type_group_property_id', 'default' => 'Id'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_insertion_type_group_property_group_name', 'default' => 'Group name'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'     => 'order',
            'type'     => CrudDataTypes::INT,
            'label'    => ['key' => 'plugin_insertion_type_group_property_group_order', 'default' => 'Order'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
    ];

    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'order' => CrudGroubByOrder::ASC,
    ];

    public function __construct() {
        parent::__construct();
    }
}
