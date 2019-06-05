<?php

namespace Blog\Controller\Backend;

use Blog\Models\Category;
use Blog\Models\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Forge\ForgeEntityManager;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\CRUD\Enum\CrudGroubByOrder;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Models\Language;
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
            'label'  => ['key' => 'plugin_blog_property_category_name', 'default' => 'Name'],
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
            'label' => ['key' => 'plugin_blog_property_category_language', 'default' => 'Language'],
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
            'name'  => 'seoUrlPath',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_blog_property_category_seoUrlPath', 'default' => 'SEO url path'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# seoUrlPath
        [
            'name'  => 'cssClass',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_blog_property_category_cssClass', 'default' => 'CSS class'],
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
            'label' => ['key' => 'plugin_blog_property_category_headerTitle', 'default' => 'Header title'],
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
            'label' => ['key' => 'plugin_blog_property_category_headerSubtext', 'default' => 'Header subtext'],
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
            'label' => ['key' => 'plugin_blog_property_category_headerImage', 'default' => 'Header image'],
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
            'label'    => ['key' => 'plugin_blog_goto', 'default' => 'Go-to'],
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
            'label'    => ['key' => 'plugin_blog_delete_notice', 'default' => 'Notice'],
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
            'label' => ['key' => 'plugin_blog_filter_category_language', 'default' => 'Select language'],
            'list'  => 'getSelectLanguages',
        ],
        'name'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_blog_filter_category_name', 'default' => 'Search in name'],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'name' => CrudGroubByOrder::ASC,
    ];
    /** @var array $selectLanguages */
    private $selectLanguages;
    /** @var array $dataPostsOfCategory */
    private $dataPostsOfCategory;

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
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction);
        if (!empty($data) && $crudAction !== 'create') {
            if (!isset($this->dataPostsOfCategory)) {
                $this->dataPostsOfCategory = [];
                /* @var ForgeEntityManager $entityManager */
                $entityManager = Oforge()->DB()->getForgeEntityManager();
                $entries       = $entityManager->getRepository(Post::class)->createQueryBuilder('p')#
                                               ->select('IDENTITY(p.category) as id, COUNT(p) as value')#
                                               ->groupBy('p.category')#
                                               ->getQuery()->getArrayResult();
                foreach ($entries as $entry) {
                    $this->dataPostsOfCategory[$entry['id']] = $entry['value'];
                }
            }
            $data['posts'] = ArrayHelper::get($this->dataPostsOfCategory, $data['id'], 0);
        }

        return $data;
    }

    /**
     * Get languages for select field.
     *
     * @return array
     * @throws Exception
     */
    protected function getSelectLanguages() {
        if (!isset($this->selectLanguages)) {
            $this->selectLanguages = [];
            /** @var LanguageService $languageService */
            $languageService = Oforge()->Services()->get('i18n.language');
            /** @var Language[] $entities */
            $entities = $languageService->list();
            foreach ($entities as $entity) {
                $this->selectLanguages[$entity->getIso()] = $entity->getName();
            }
        }

        return $this->selectLanguages;
    }

    /** @inheritDoc */
    protected function handleDeleteAction(Response $response, string $entityID) {
        try {
            /** @var Category $category */
            $category = $this->crudService->getById($this->model, $entityID);
            if (!isset($category)) {
                Oforge()->View()->Flash()->addMessage('error', sprintf(#
                    I18N::translate('plugin_blog_category_not_found', 'Category with ID "%s" not found.'),#
                    $entityID#
                ));

                return $this->redirect($response, 'index');
            }
            $postCount = $category->getPosts()->count();
            if ($postCount > 0) {
                Oforge()->View()->Flash()->addMessage('warning', sprintf(#
                    I18N::translate('plugin_blog_category_not_deleted_has_posts', 'Category with ID "%s" can not be deleted because it still contains posts.'),#
                    $entityID#
                ));

                return $this->redirect($response, 'index');
            }

            return parent::handleDeleteAction($response, $entityID);
        } catch (ORMException $exception) {
            Oforge()->View()->Flash()->addExceptionMessage('error', I18N::translate('backend_crud_error', 'An error has occurred.'), $exception);

            return $this->redirect($response, 'delete', ['id' => $entityID]);
        }
    }

}
