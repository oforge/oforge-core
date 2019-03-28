<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Controller\Backend\CrudController;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class SnippetsController
 *
 * @package Oforge\Engine\Modules\I18n\Controller\Backend
 */
class SnippetsController extends CrudController {
    /** @var string $model */
    protected $model = Snippet::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name' => 'id',
            'type' => 'int',
            'crud' => [
                'index'  => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
            ],
        ],
        [
            'name' => 'scope',
            'type' => 'select',
            'list' => 'getSelectLanguages',
            'crud' => [
                'index'  => 'readonly',
                'create' => 'editable',
                'update' => 'readonly',
            ],
        ],
        [
            'name' => 'name',
            'type' => 'string',
            'crud' => [
                'index'  => 'readonly',
                'create' => 'editable',
                'update' => 'readonly',
            ],
        ],
        [
            'name' => 'value',
            'type' => 'string',
            'crud' => [
                'index'  => 'editable',
                'create' => 'editable',
                'update' => 'editable',
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
