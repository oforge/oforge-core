<?php

namespace Blog\Controller\Backend;

use Blog\Models\Category;
use Blog\Services\CategoryService;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroupByOrder;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Services\LanguageService;
use Slim\Http\Response;

/**
 * Class CategoryController
 *
 * @package Blog\Controller\Backend\Blog
 * @EndpointClass(path="/backend/blog/categories", name="backend_blog_categories", assetScope="Backend")
 */
class CategoryController extends BaseCrudController {
    /** @var string $model */
    protected $model = Category::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'   => 'name',
            'type'   => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_blog_property_category_name',
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
                'maxlength' => null,
            ],
        ],# name
        [
            'name'  => 'language',
            'type'  => CrudDataTypes::SELECT,
            'label' => [
                'key'     => 'plugin_blog_property_category_language',
                'default' => [
                    'en' => 'Language',
                    'de' => 'Sprache',
                ],
            ],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectLanguages',
        ],# language
        [
            'name'   => 'active',
            'type'   => CrudDataTypes::BOOL,
            'label' => [
                'key'     => 'plugin_blog_property_category__active',
                'default' => [
                    'en' => 'Active',
                    'de' => 'Aktiv',
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
                'default' => true,
            ],
        ],# active
        [
            'name'  => 'seoUrlPath',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_blog_property_category_seoUrlPath',
                'default' => [
                    'en' => 'SEO url path',
                    'de' => 'SEO URL-Pfad',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# seoUrlPath
        [
            'name'  => 'icon',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_blog_property_category_icon',
                'default' => [
                    'en' => 'Icon',
                    'de' => 'Icon',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# cssClass
        [
            'name'  => 'cssClass',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_blog_property_category_cssClass',
                'default' => [
                    'en' => 'CSS classes',
                    'de' => 'CSS Klassen',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# cssClass
        [
            'name'  => 'headerTitle',
            'type'  => CrudDataTypes::STRING,
            'label' => [
                'key'     => 'plugin_blog_property_category_headerTitle',
                'default' => [
                    'en' => 'Header title',
                    'de' => 'Header-Titel',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# headerTitle
        [
            'name'  => 'headerSubtext',
            'type'  => CrudDataTypes::TEXT,
            'label' => [
                'key'     => 'plugin_blog_property_category_headerSubtext',
                'default' => [
                    'en' => 'Header subtext',
                    'de' => 'Header-Subtext',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# headerSubtext
        [
            'name'  => 'headerImage',
            'type'  => CrudDataTypes::IMAGE,
            'label' => [
                'key'     => 'plugin_blog_property_category_headerImage',
                'default' => [
                    'en' => 'Header image',
                    'de' => 'Headerbild',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# headerImage
        [
            'name'     => 'goto',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'plugin_blog_goto',
                'default' => [
                    'en' => 'Go-to',
                    'de' => 'Gehe zu',
                ],
            ],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'off',
            ],
            'renderer' => [
                'custom' => 'Plugins/Blog/Backend/Category/Components/Goto.twig',
            ],
        ],# goto
        [
            'name'     => 'notice',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'plugin_blog_delete_notice',
                'default' => [
                    'en' => 'Notice',
                    'de' => 'Anmerkung',
                ],
            ],
            'crud'     => [
                'index'  => 'off',
                'view'   => 'off',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Blog/Backend/Category/Components/DeleteCategoryNotice.twig',
            ],
        ],# notice
    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'language' => [
            'type'  => CrudFilterType::SELECT,
            'label'   => [
                'key'     => 'plugin_blog_filter_category_language',
                'default' => [
                    'en' => 'Search in language',
                    'de' => 'Sprache auswählen',
                ],
            ],
            'list'  => 'getSelectLanguages',
        ],
        'name'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'plugin_blog_filter_category_name',
                'default' => [
                    'en' => 'Search in name',
                    'de' => 'Im Namen suchen',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'name' => CrudGroupByOrder::ASC,
    ];

    public function __construct() {
        parent::__construct();
    }

    /**
     * @inheritDoc
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    protected function modifyPropertyConfig(array $config) {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        if ($config['name'] === 'name') {
            $maxlength = $configService->get('blog_category_maxlength_name');
            if ($maxlength > 0) {
                $config['editor']['maxlength'] = $maxlength;
            }
        }

        return $config;
    }

    /** @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction, array $queryParams = []) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction, $queryParams);
        if (!empty($data) && $crudAction !== 'create') {
            if (!isset($this->filterSelectData['postsOfCategory'])) {
                try {
                    /** @var CategoryService $categoryService */
                    $categoryService = Oforge()->Services()->get('blog.category');

                    $this->filterSelectData['postsOfCategory'] = $categoryService->getFilterDataPostCountOfCategories();
                } catch (ServiceNotFoundException $exception) {
                    $this->filterSelectData['postsOfCategory'] = [];
                }
            }
            $data['posts'] = ArrayHelper::get($this->filterSelectData['postsOfCategory'], $data['id'], 0);
        }

        return $data;
    }

    /**
     * Get languages for select field.
     *
     * @return array
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

    /** @inheritDoc */
    protected function handleDeleteAction(Response $response, string $entityID) {
        try {
            /** @var Category $category */
            $category = $this->crudService->getById($this->model, $entityID);
            if (!isset($category)) {
                Oforge()->View()->Flash()->addMessage('error', sprintf(#
                    I18N::translate('plugin_blog_category_not_found', [
                        'en' => 'Category with ID "%s" not found.',
                        'de' => 'Kategorie mit der ID "%s" wurde nicht gefunden.',
                    ]),#
                    $entityID#
                ));

                return $this->redirect($response, 'index');
            }
            $postCount = $category->getPosts()->count();
            if ($postCount > 0) {
                Oforge()->View()->Flash()->addMessage('warning', sprintf(#
                    I18N::translate('plugin_blog_category_not_deleted_has_posts', [
                        'en' => 'Category with ID "%s" can not be deleted because it still contains posts.',
                        'de' => 'Kategorie mit der ID "%s" kann nicht gelöscht werden, da sie noch Beiträge enthält.',
                    ]),#
                    $entityID#
                ));

                return $this->redirect($response, 'index');
            }

            return parent::handleDeleteAction($response, $entityID);
        } catch (ORMException $exception) {
            Oforge()->View()->Flash()->addExceptionMessage('error', I18N::translate('backend_crud_error', [
                'en' => 'An error has occurred.',
                'de' => 'Ein Fehler ist aufgetreten.',
            ]), $exception);

            return $this->redirect($response, 'delete', ['id' => $entityID]);
        }
    }

}
