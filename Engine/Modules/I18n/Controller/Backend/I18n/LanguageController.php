<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;

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
        if (!isset($this->snippets)) {
            $this->snippets = [];
            try {
                /* @var EntityManager $entityManager */
                $entityManager = Oforge()->DB()->getEntityManager();
                $queryBuilder  = $entityManager->getRepository(Snippet::class)->createQueryBuilder('s');
                $entries       = $queryBuilder->select('s.scope, COUNT(s) as value')->groupBy('s.scope')->getQuery()->getArrayResult();
                foreach ($entries as $entry) {
                    $this->snippets[$entry['scope']] = $entry['value'];
                }
            } catch (ORMException $exception) {
            }
        }
        $data['snippets'] = ArrayHelper::get($this->snippets, $data['iso'], 0);

        return $data;
    }

}
