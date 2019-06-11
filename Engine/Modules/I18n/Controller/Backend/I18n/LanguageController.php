<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class LanguageController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 * @EndpointClass(path="/backend/i18n/languages", name="backend_i18n_languages", assetScope="Backend")
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
            'name'  => 'iso',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'module_i18n_language_iso', 'default' => 'ISO'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'name',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'module_i18n_language_name', 'default' => 'Name'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'active',
            'type'  => CrudDataTypes::BOOL,
            'label' => ['key' => 'module_i18n_language_active', 'default' => 'Active'],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'     => 'action',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => 'backend_i18n_snippets',
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

    /** @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction);
        if (!isset($this->filterSelectData['snippets'])) {
            $this->snippets = [];
            try {
                /** @var LanguageService $languageService */
                $languageService = Oforge()->Services()->get('i18n.language');

                $this->filterSelectData['snippets'] = $languageService->getFilterDataSnippetsOfLanguage();
            } catch (ServiceNotFoundException $exception) {
                $this->filterSelectData['snippets'] = [];
            }
        }
        $data['snippets'] = ArrayHelper::get($this->filterSelectData['snippets'], $data['iso'], 0);

        return $data;
    }

}
