<?php

namespace FrontendUserManagement\Controller\Backend;

use FrontendUserManagement\Models\FindyourhorseUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;

/**
 * Class BackendFindyourhorseUserController
 * @package FrontendUserManagement\Controller\Backend
 *
 * @EndpointClass(path="/backend/findyourhorse", name="backend_findyourhorse", assetScope="Backend")
 */
class BackendFindyourhorseUserController extends BaseCrudController {
    protected $model = FindyourhorseUser::class;

    protected $modelProperties = [
        [
            'name'  => 'id',
            'type'  => CrudDataTypes::INT,
            'label' => ['key' => 'plugin_frontend_user_management_property_id', 'default' => 'Id'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'fyhMail',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'findyourhorse_email', 'default' => 'FYH-Email'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'     => 'user',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'backend_findyourhorse_ayh_mail', 'default' => 'AYH-Email'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/FrontendUserManagement/Backend/BackendFrontendUserManagement/CRUD/RenderUser.twig',
            ],
        ],
    ];

    /**
     * @var array $crudActions Keys of 'add|edit|delete'
     */
    protected $crudActions = [
        'index'  => true,
        'create' => false,
        'view'   => true,
        'update' => false,
        'delete' => true,
    ];

    public function __construct() {
        parent::__construct();
    }

    public function initPermissions() {
        parent::initPermissions();
    }
}
