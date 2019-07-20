<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Exception;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class SnippetsController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 * @EndpointClass(path="/backend/i18n/snippets", name="backend_i18n_snippets", assetScope="Backend")
 */
class SnippetsController extends BaseCrudController {
    /** @var string $model */
    protected $model = Snippet::class;
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
            'name'  => 'scope',
            'type'  => CrudDataTypes::SELECT,
            'label' => [
                'key'     => 'module_i18n_snippet_scope',
                'default' => [
                    'en' => 'Language',
                    'de' => 'Sprache',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectLanguages',
            'editor' => [
                'required' => true,
            ],
        ],
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'module_i18n_snippet_name',
                'default' => [
                    'en' => 'Name',
                    'de' => 'Name',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'editor' => [
                'required' => true,
            ],
        ],
        [
            'name'  => 'value',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'module_i18n_snippet_value',
                'default' => [
                    'en' => 'Value',
                    'de' => 'Wert',
                ],
            ],
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
        'scope' => [
            'type'  => CrudFilterType::SELECT,
            'label' => [
                'key'     => 'module_i18n_filter_snippet_scope',
                'default' => [
                    'en' => 'Select scope',
                    'de' => 'Sprache auswählen',
                ],
            ],
            'list'  => 'getSelectLanguages',
        ],
        'name'  => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'module_i18n_filter_snippet_name',
                'default' => [
                    'en' => 'Search in name',
                    'de' => 'Suche in Name',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'value' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'module_i18n_filter_snippet_value',
                'default' => [
                    'en' => 'Search in value',
                    'de' => 'Suche in Wert',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get languages for select field.
     *
     * @return array
     * @throws Exception
     */
    protected function getSelectLanguages() : array {
        try {
            /** @var LanguageService $languageService */
            $languageService = Oforge()->Services()->get('i18n.language');

            return $languageService->getFilterDataLanguages();
        } catch (ServiceNotFoundException $exception) {
            return [];
        }
    }

}
