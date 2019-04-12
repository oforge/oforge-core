<?php

namespace Oforge\Engine\Modules\CRUD\Controller\Backend\CRUD\Test;

use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Models\CrudTest;

/**
 * Class CrudTestWriteController
 *
 * @package Oforge\Engine\Modules\CRUD\Controller\Backend
 */
class WriteController extends BaseCrudController {
    /** @var string $baseEndpointName */
    protected static $baseEndpointName = 'backend_crudtest_write';
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
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeText',
            'type' => CrudDataTypes::TEXT,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeInteger',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeSmallint',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeBigint',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeBoolean',
            'type' => CrudDataTypes::BOOL,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeDecimal',
            'type' => CrudDataTypes::DECIMAL,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'typeFloat',
            'type' => CrudDataTypes::FLOAT,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        // [
        //     'name' => 'typeDate',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeTime',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeDatetime',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeObject',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeArray',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeSimpleArray',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
        // [
        //     'name' => 'typeJsonArray',
        //     'type' => CrudDataTypes::,
        //     'crud' => [
        //         'index'  => 'editable',
        //         'view'  => 'readonly',
        //         'create' => 'editable',
        //         'update' => 'editable',
        //         'delete' => 'readonly',
        //     ],
        // ],
    ];

}
