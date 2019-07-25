<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Helper\I18N;
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
            'name'   => 'iso',
            'type'   => CrudDataTypes::STRING,
            'label'  => [
                'key'     => 'module_i18n_language_iso',
                'default' => [
                    'en' => 'ISO',
                    'de' => 'ISO',
                ],
            ],
            'crud'   => [
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
            'name'   => 'name',
            'type'   => CrudDataTypes::STRING,
            'label'  => [
                'key'     => 'module_i18n_language_name',
                'default' => [
                    'en' => 'Name',
                    'de' => 'Name',
                ],
            ],
            'crud'   => [
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
            'name'  => 'active',
            'type'  => CrudDataTypes::BOOL,
            'label' => [
                'key'     => 'module_i18n_language_active',
                'default' => [
                    'en' => 'Active',
                    'de' => 'Aktiv',
                ],
            ],
            'crud'  => [
                'index'  => 'editable',
                'view'   => 'editable',
                'create' => 'off',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],
        [
            'name'  => 'default',
            'type'  => CrudDataTypes::BOOL,
            'label' => [
                'key'     => 'module_i18n_language_default',
                'default' => [
                    'en' => 'Standard',
                    'de' => 'Standard',
                ],
            ],
            'crud'  => [
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
            'label'    => [
                'key'     => 'backend_i18n_snippets',
                'default' => [
                    'en' => 'Text snippets',
                    'de' => 'Textschnipsel',
                ],
            ],
            'crud'     => [
                'index' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Backend/I18n/Language/Index/GotoSnippets.twig',
            ],
        ],
    ];

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

    /** @inheritDoc */
    protected function handleIndexUpdate(array $postData) {
        $list = $postData['data'];
        $this->handleFileUploads($list, 'index');
        foreach ($list as $entityID => $data) {
            $list[$entityID] = $this->convertData($data, 'update');
        }
        $postData['data'] = $list;
        try {
            $this->crudService->update($this->model, $postData);
            /** @var LanguageService $languageService */
            try {
                $languageService = Oforge()->Services()->get('i18n.language');
                // $languageService->updateCurrentLanguage();
            } catch (ServiceNotFoundException $exception) {
                Oforge()->Logger()->logException($exception);
            }
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('backend_crud_msg_bulk_update_success', [
                'en' => 'Entities successfully bulk updated.',
                'de' => 'Alle Elemente wurden erfolgreich aktualisiert.',
            ]));
        } catch (Exception $exception) {
            Oforge()->View()->Flash()->addExceptionMessage('error', I18N::translate('backend_crud_msg_bulk_update_failed', [
                'en' => 'Entities bulk update failed.',
                'de' => 'Aktualisierung der Elemente fehlgeschlagen.',
            ]), $exception);
            Oforge()->View()->Flash()->setData($this->moduleModelName, $postData['data']);
        }
    }

}
