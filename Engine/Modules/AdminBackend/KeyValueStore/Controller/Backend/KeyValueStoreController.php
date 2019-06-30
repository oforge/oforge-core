<?php

namespace Oforge\Engine\Modules\AdminBackend\KeyValueStore\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Models\Store\KeyValue;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroubByOrder;

/**
 * Class KeyValueStoreController
 *
 * @package Oforge\Engine\Modules\AdminBackend\KeyValueStore\Controller\Backend
 * @EndpointClass(path="/backend/key-value-store", name="backend_key_value_store", assetScope="Backend")
 */
class KeyValueStoreController extends BaseCrudController {
    /** @var string $model */
    protected $model = KeyValue::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'backend_keyvaluestore_name', 'default' => 'Name'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'value',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'backend_keyvaluestore_value', 'default' => 'Value'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'name' => [
            'type'  => CrudFilterType::TEXT,
            'label' => ['key' => 'backend_keyvaluestore_filter_name', 'default' => 'Search in name'],
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'name' => CrudGroubByOrder::ASC,
    ];
    /** @var array $crudActions */
    protected $crudActions = [
        'index'  => true,
        'create' => true,
        'view'   => true,
        'update' => true,
        'delete' => false,
    ];

}
