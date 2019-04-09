<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Models\Language;

/**
 * Class LanguageController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 */
class LanguageController extends BaseCrudController {
    /** @var string $model */
    protected $model = Language::class;
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
            'name' => 'iso',
            'type' => CrudDataTypes::STRING,
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'name',
            'type' => CrudDataTypes::STRING,
            'crud' => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name' => 'active',
            'type' => CrudDataTypes::BOOL,
            'crud' => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'     => 'action',
            'type'     => CrudDataTypes::CUSTOM,
            'label' => 'backend_i18n_snippets',
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/I18n/Language/Index/GotoSnippets.twig',
            ],
        ],
    ];

    public function __construct() {
        parent::__construct();
    }

}
