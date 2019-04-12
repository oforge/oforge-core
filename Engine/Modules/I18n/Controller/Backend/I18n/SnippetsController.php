<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class SnippetsController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 */
class SnippetsController extends BaseCrudController {
    /** @var string $baseEndpointName */
    protected static $baseEndpointName = 'backend_i18n_snippets';
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
            'name'          => 'scope',
            'type'          => CrudDataTypes::SELECT,
            'label'         => ['key' => 'crud_i18n_snippet_scope', 'default' => 'Scope'],
            'crud'          => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'list'          => 'getSelectLanguages',
            'listI18nLabel' => false,
        ],
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'crud_i18n_snippet_name', 'default' => 'Name'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'value',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'crud_i18n_snippet_value', 'default' => 'Value'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get languages for select field.
     *
     * @return array
     */
    protected function getSelectLanguages() {
        $result = [];
        try {
            /** @var LanguageService $languageService */
            $languageService = Oforge()->Services()->get('languages');
            /** @var Language[] $entities */
            $entities = $languageService->list();
            foreach ($entities as $entity) {
                $result[$entity->getIso()] = $entity->getName();
            }
        } catch (ServiceNotFoundException $e) {
        }

        return $result;
    }

}
