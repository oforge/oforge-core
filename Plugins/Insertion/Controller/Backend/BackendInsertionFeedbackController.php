<?php

namespace Insertion\Controller\Backend;

use Insertion\Models\InsertionFeedback;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;

/**
 * Class BackendInsertionFeedbackController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 * @EndpointClass(path="/backend/insertions/feedback", name="backend_insertions_feedback", assetScope="Backend")
 */
class BackendInsertionFeedbackController extends BaseCrudController {
    /** @var string $model */
    protected $model = InsertionFeedback::class;
    /** @var array $modelProperties */
    protected $modelProperties = [

        [
            'name' => 'id',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index' => 'readonly',
            ],
        ],
        [
            'name' => 'text',
            'type' => CrudDataTypes::STRING,
            'crud' => [
                'index'  => 'readonly',
                'delete' => 'readonly',
                'view'   => 'readonly',
            ]
        ],
        [
            'name' => 'rating',
            'type' => CrudDataTypes::INT,
            'crud' => [
                'index'  => 'readonly',
                'delete' => 'readonly',
                'view'   => 'readonly',
            ]
        ]
    ];
    protected $indexFilter = [

        'text'  => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'plugin_insertion_feedback_filter_text',
                'default' => [
                    'en' => 'Search in text',
                    'de' => 'Suche im Text',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /**
     * Configuration of the orderBy on the index view.
     *      protected $indexOrderBy = [
     *          'propertyName' => CrudGroupByOrder::ASC|DESC,
     *      ];
     *
     * @var array $indexOrderBy
     */
    protected $indexOrderBy = [
        'id' => CrudGroupByOrder::DESC,
    ];
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
        $this->ensurePermissions(['approveInsertionAction','disapproveInsertionAction'], BackendUser::ROLE_MODERATOR);
    }
}
