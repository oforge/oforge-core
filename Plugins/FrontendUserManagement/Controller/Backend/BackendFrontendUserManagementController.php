<?php

namespace FrontendUserManagement\Controller\Backend;

use FrontendUserManagement\Models\User;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroubByOrder;

/**
 * Class CategoryController
 *
 * @package FrontendUserManagement\Controller\Backend\FrontendUserManagement
 * @EndpointClass(path="/backend/frontendusers", name="backend_frontend_user_management", assetScope="Backend")
 */
class BackendFrontendUserManagementController extends BaseCrudController {
    /** @var string $model */
    protected $model = User::class;
    /** @var array $modelProperties */
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
            'name'  => 'email',
            'type'  => CrudDataTypes::EMAIL,
            'label' => ['key' => 'plugin_frontend_user_management_property_contact_email', 'default' => 'Account email'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'     => 'accountEmail',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_frontend_user_management_property_contact_email', 'default' => 'Contact email'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/FrontendUserManagement/Backend/BackendFrontendUserManagement/CRUD/RenderContactEmail.twig',
            ],
        ],
        [
            'name'     => 'firstName',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_frontend_user_management_property_first_name', 'default' => 'First name'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/FrontendUserManagement/Backend/BackendFrontendUserManagement/CRUD/RenderFirstName.twig',
            ],
        ],
        [
            'name'     => 'lastName',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_frontend_user_management_property_last_name', 'default' => 'Last name'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/FrontendUserManagement/Backend/BackendFrontendUserManagement/CRUD/RenderLastName.twig',
            ],
        ],
        [
            'name'     => 'nickName',
            'type'     => CrudDataTypes::STRING,
            'label'    => ['key' => 'plugin_frontend_user_management_property_nickname', 'default' => 'Nickname'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/FrontendUserManagement/Backend/BackendFrontendUserManagement/CRUD/RenderNickName.twig',
            ],
        ],
        [
            'name'     => 'phoneNumber',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_frontend_user_management_property_phone_number', 'default' => 'Phone number'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/FrontendUserManagement/Backend/BackendFrontendUserManagement/CRUD/RenderPhoneNumber.twig',
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

    /** @var array $indexFilter */
    protected $indexFilter = [
        'contactEmail' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_frontend_user_management_filter_email', 'default' => 'Search in email'],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'lastName'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_frontend_user_management_filter_last_name', 'default' => 'Search in last name'],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'nickName'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_frontend_user_management_filter_nickname', 'default' => 'Search in nickname'],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'id' => CrudGroubByOrder::ASC,
    ];

    public function __construct() {
        parent::__construct();
    }
}
