<?php

namespace Insertion\Controller\Backend;

use Insertion\Models\InsertionFeedback;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;


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
                'index' => 'readonly',
            ]
        ]
    ];

    public function __construct() {
        parent::__construct();
    }

    public function initPermissions() {
        parent::initPermissions();
        $this->ensurePermissions(['approveInsertionAction','disapproveInsertionAction'], BackendUser::ROLE_MODERATOR);
    }
}
