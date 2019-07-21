<?php

namespace Faq\Controller\Backend;

use Faq\Models\FaqModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroubByOrder;

/**
 * Class AccountController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/backend/faq", name="backend_faq", assetScope="Backend")
 */
class BackendFaqController extends BaseCrudController {
    /** @var string $model */
    protected $model = FaqModel::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'id',
            'type'  => CrudDataTypes::INT,
            'label' => ['key' => 'plugin_frontend_faq_property_id', 'default' => 'Id'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'readonly',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'question',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_frontend_faq_property_question', 'default' => 'Question'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'answer',
            'type'  => CrudDataTypes::HTML,
            'label' => ['key' => 'plugin_frontend_faq_property_answer', 'default' => 'Answer'],
            'crud'  => [
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
        'id' => CrudGroubByOrder::ASC,
    ];

    public function __construct() {
        parent::__construct();
    }

}