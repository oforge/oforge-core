<?php

namespace Oforge\Engine\Modules\I18n\Controller\Backend\I18n;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\I18n\Services\LanguageService;
use Slim\Http\Request;
use Slim\Http\Response;

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
            'name'   => 'scope',
            'type'   => CrudDataTypes::SELECT,
            'label'  => [
                'key'     => 'module_i18n_snippet_scope',
                'default' => [
                    'en' => 'Language',
                    'de' => 'Sprache',
                ],
            ],
            'crud'   => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'list'   => 'getSelectLanguages',
            'editor' => [
                'required' => true,
            ],
        ],
        [
            'name'   => 'name',
            'type'   => CrudDataTypes::STRING,
            'label'  => [
                'key'     => 'module_i18n_snippet_name',
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
                    'de' => 'Suche im Namen',
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

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response|void
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/comparator")
     */
    public function comparatorAction(Request $request, Response $response) {
        $postData = $request->getParsedBody();
        if ($request->isPost() && !empty($postData)) {
            $created = 0;
            $updated = 0;
            $failed  = 0;
            if (isset($postData['data_create'])) {
                foreach ($postData['data_create'] as $data) {
                    $data = $this->convertData($data, 'update');
                    try {
                        $this->crudService->create($this->model, $data);
                        $created++;
                    } catch (Exception $exception) {
                        $failed++;
                        Oforge()->View()->Flash()->addExceptionMessage('error', sprintf(#
                            I18N::translate('module_i18n_snippets_comparator_msg_fail_create', [
                                'en' => 'Could not create snippet "%s".',
                                'de' => 'Snippet "%s" konnte nicht erstellt werden.',
                            ]), ArrayHelper::get($data, 'name')#
                        ), $exception);
                    }
                }
            }
            foreach ($postData['data_update'] as $entityID => $data) {
                $data['id'] = $entityID;
                $data       = $this->convertData($data, 'update');
                try {
                    $this->crudService->update($this->model, $data);
                    $updated++;
                } catch (Exception $exception) {
                    $failed++;
                    Oforge()->View()->Flash()->addExceptionMessage('error', sprintf(#
                        I18N::translate('module_i18n_snippets_comparator_msg_fail_update', [
                            'en' => 'Could not update snippet "%s".',
                            'de' => 'Snippet "%s" konnte nicht aktualisiert werden.',
                        ]), $entityID#
                    ), $exception);
                }
            }
            Oforge()->View()->Flash()->addMessage('info', sprintf(#
                I18N::translate('module_i18n_snippets_comparator_update_msg', [
                    'en' => '%s created, %s updated, %s failed.',
                    'de' => '%s erstellt, %s aktualisiert, %s fehlgeschlagen.',
                ]),#
                $created, $updated, $failed#
            ));

            return RouteHelper::redirect($response, 'backend_i18n_snippets_comparator', [], $request->getQueryParams());
        }
        $items          = [];
        $language1Items = 0;
        $language2Items = 0;
        $language1      = $request->getQueryParam('language1');
        $language2      = $request->getQueryParam('language2');
        $filterName     = $request->getQueryParam('name', '');
        if (!empty($language1) && !empty($language2)) {
            $fill = function ($language, &$counter) use (&$items, $filterName) {
                $criteria = [
                    'scope' => [
                        'comparator' => CrudFilterComparator::EQUALS,
                        'value'      => $language,
                    ],
                ];
                if (!empty($filterName)) {
                    $criteria['name'] = [
                        'comparator' => CrudFilterComparator::LIKE,
                        'value'      => $filterName,
                    ];
                }
                /** @var Snippet[] $entities */
                $entities = $this->crudService->list($this->model, $criteria, ['name' => 'ASC']);
                foreach ($entities as $entity) {
                    $nameID = str_replace(' ', '_', $entity->getName()); #hash('sha512', $entity->getName());
                    if (!isset($items[$nameID])) {
                        $items[$nameID] = [
                            'name'   => $entity->getName(),
                            'scopes' => [],
                        ];
                    }
                    $items[$nameID]['scopes'][$language] = [
                        'id'    => $entity->getId(),
                        'value' => $entity->getValue(),
                    ];
                }
                $counter = count($entities);
            };
            $fill($language1, $language1Items);
            $fill($language2, $language2Items);
        }
        /** @var LanguageService $languageService */
        $languageService     = Oforge()->Services()->get('i18n.language');
        $filterDataLanguages = $languageService->getFilterDataLanguages();

        $filter = [
            'language1' => [
                'type'  => CrudFilterType::SELECT,
                'label' => [
                    'key'     => 'module_i18n_filter_snippet_scope1',
                    'default' => [
                        'en' => 'Select language1',
                        'de' => 'Sprache1 auswählen',
                    ],
                ],
                'list'  => $filterDataLanguages,
            ],
            'swap'      => [
                'type'  => 'Button',
                'label' => [
                    'key'     => 'module_i18n_filter_snippet_comparator_swap',
                    'default' => [
                        'en' => 'Swap languages',
                        'de' => 'Sprachen tauschen',
                    ],
                ],
            ],
            'language2' => [
                'type'  => CrudFilterType::SELECT,
                'label' => [
                    'key'     => 'module_i18n_filter_snippet_scope2',
                    'default' => [
                        'en' => 'Select language2',
                        'de' => 'Sprache2 auswählen',
                    ],
                ],
                'list'  => $filterDataLanguages,
            ],
            'name'      => [
                'type'    => CrudFilterType::TEXT,
                'label'   => [
                    'key'     => 'module_i18n_filter_snippet_name',
                    'default' => [
                        'en' => 'Search in name',
                        'de' => 'Suche im Namen',
                    ],
                ],
                'compare' => CrudFilterComparator::LIKE,
            ],
        ];

        Oforge()->View()->assign([
            'i18nComparator' => [
                'items'     => $items,
                'count'     => [$language1 => $language1Items, $language2 => $language2Items],
                'languages' => $filterDataLanguages,
                'filter'    => $filter,
            ],
        ]);
    }

    public function initPermissions() {
        parent::initPermissions();
        $this->ensurePermission('comparatorAction', BackendUser::ROLE_MODERATOR);
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
