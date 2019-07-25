<?php

namespace FrontendUserManagement\Controller\Backend;

use FrontendUserManagement\Models\NickNameValue;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;

/**
 * Class CategoryController
 *
 * @package FrontendUserManagement\Controller\Backend\FrontendUserManagement
 * @EndpointClass(path="/backend/nickname", name="backend_frontend_user_management_nickname_generator", assetScope="Backend")
 */
class BackendNickNameGeneratorController extends BaseCrudController {
    /** @var string $model */
    protected $model = NickNameValue::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'value',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'value',
                'default' => [
                    'en' => 'Value',
                    'de' => 'Wert',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'editable',
            ],
        ],
        [
            'name'  => 'order',
            'type'  => CrudDataTypes::INT,
            'label' => [
                'key'     => 'order',
                'default' => [
                    'en' => 'Order',
                    'de' => 'Reihenfolge',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'editable',
            ],
        ],
    ];

    /**
     * @var array $crudActions Keys of 'add|edit|delete'
     */
    protected $crudActions = [
        'index'  => true,
        'create' => true,
        'view'   => true,
        'update' => true,
        'delete' => true,
    ];

    public function __construct() {
        parent::__construct();
    }

}
