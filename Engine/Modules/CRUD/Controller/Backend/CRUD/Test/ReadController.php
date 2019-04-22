<?php

namespace Oforge\Engine\Modules\CRUD\Controller\Backend\CRUD\Test;

use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Models\CrudTest;

/**
 * Class CrudTestReadController
 *
 * @package Oforge\Engine\Modules\CRUD\Controller\Backend
 */
class ReadController extends BaseCrudController {
    /** @var string $baseEndpointName */
    protected static $baseEndpointName = 'backend_crudtest_read';
    /** @var string $model */
    protected $model = CrudTest::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name' => 'id',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeString',
            'type' => CrudDataTypes::STRING,
            'label' => ['key' => 'crud_crudtest_string', 'default' => 'String'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeText',
            'type' => CrudDataTypes::TEXT,
            'label' => ['key' => 'crud_crudtest_text', 'default' => 'Text'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeHtml',
            'type' => CrudDataTypes::HTML,
            'label' => ['key' => 'crud_crudtest_html', 'default' => 'HTML'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeInteger',
            'type' => CrudDataTypes::INT,
            'label' => ['key' => 'crud_crudtest_integer', 'default' => 'Integer'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeSmallint',
            'type' => CrudDataTypes::INT,
            'label' => ['key' => 'crud_crudtest_smallint', 'default' => 'Smallint'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeBigint',
            'type' => CrudDataTypes::INT,
            'label' => ['key' => 'crud_crudtest_bigint', 'default' => 'Bigint'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeBoolean',
            'type' => CrudDataTypes::BOOL,
            'label' => ['key' => 'crud_crudtest_boolean', 'default' => 'Boolean'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeDecimal',
            'type' => CrudDataTypes::DECIMAL,
            'label' => ['key' => 'crud_crudtest_Decimal', 'default' => 'Decimal'],
            'crud' => [
                'index'  => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeFloat',
            'type' => CrudDataTypes::FLOAT,
            'label' => ['key' => 'crud_crudtest_float', 'default' => 'Float'],
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        // [
        //     'name' => 'typeDate',
        //     'type' => CrudDataTypes::,
        //     'label' => ['key' => 'crud_crudtest_date', 'default' => 'Date'],
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeTime',
        //     'type' => CrudDataTypes::,
        //     'label' => ['key' => 'crud_crudtest_time', 'default' => 'Time'],
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeDatetime',
        //     'label' => ['key' => 'crud_crudtest_datetime', 'default' => 'Datetime'],
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeObject',
        //     'type' => CrudDataTypes::,
        //     'label' => ['key' => 'crud_crudtest_object', 'default' => 'Object'],
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeArray',
        //     'type' => CrudDataTypes::,
        //     'label' => ['key' => 'crud_crudtest_array', 'default' => 'Array'],
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeSimpleArray',
        //     'type' => CrudDataTypes::,
        //     'label' => ['key' => 'crud_crudtest_simplearray', 'default' => 'SimpleArray'],
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeJsonArray',
        //     'type' => CrudDataTypes::,
        //     'label' => ['key' => 'crud_crudtest_jsonarray', 'default' => 'JsonArray'],
        //     'crud' => [
        //         'index'  => 'readonly',
        //         'view'  => 'readonly',
        //         'create' => 'readonly',
        //         'update' => 'readonly',
        //         'delete' => 'readonly',
        //     ],
        // ],
    ];

}
