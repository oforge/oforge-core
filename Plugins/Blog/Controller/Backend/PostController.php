<?php

namespace Blog\Controller\Backend;

use Blog\Models\Category;
use Blog\Models\Comment;
use Blog\Models\Post;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
 * Class PostController
 *
 * @package Blog\Controller\Backend\Blog
 * @EndpointClass(path="/backend/blog/posts", name="backend_blog_posts", assetScope="Backend")
 */
class PostController extends BaseCrudController {
    /** @var string $model */
    protected $model = Post::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'created',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_blog_property_post_created', 'default' => 'Created'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],# created
        [
            'name'  => 'updated',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_blog_property_post_updated', 'default' => 'Updated'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],# updated
        [
            'name'  => 'language',
            'type'  => CrudDataTypes::SELECT,
            'label' => ['key' => 'plugin_blog_property_post_language', 'default' => 'Language'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'off',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectLanguages',
        ],# language
        [
            'name'  => 'category',
            'type'  => CrudDataTypes::SELECT,
            'label' => ['key' => 'plugin_blog_property_post_category', 'default' => 'Category'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectCategories',
        ],# category
        [
            'name'  => 'seoUrlPath',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_blog_property_post_seoUrlPath', 'default' => 'SEO url path'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# seoUrlPath
        [
            'name'   => 'headerTitle',
            'type'   => CrudDataTypes::STRING,
            'label'  => ['key' => 'plugin_blog_property_post_headerTitle', 'default' => 'Header title'],
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
        ],# headerTitle
        [
            'name'  => 'headerSubtext',
            'type'  => CrudDataTypes::HTML,
            'label' => ['key' => 'plugin_blog_property_post_headerSubtext', 'default' => 'Header subtext'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# headerSubtext
        [
            'name'  => 'headerImage',
            'type'  => CrudDataTypes::IMAGE,
            'label' => ['key' => 'plugin_blog_property_post_headerImage', 'default' => 'Header image'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# headerImage
        [
            'name'  => 'excerpt',
            'type'  => CrudDataTypes::TEXT,
            'label' => ['key' => 'plugin_blog_property_post_excerpt', 'default' => 'Excerpt'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# excerpt
        [
            'name'  => 'content',
            'type'  => CrudDataTypes::HTML,
            'label' => ['key' => 'plugin_blog_property_post_content', 'default' => 'Content'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# content
        [
            'name'  => 'author',
            'type'  => CrudDataTypes::SELECT,
            'label' => ['key' => 'plugin_blog_property_post_author', 'default' => 'Author'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectBackendUsers',
        ],# author
        [
            'name'     => 'rating',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => ['key' => 'plugin_blog_property_post_rating', 'default' => 'Rating'],
            'crud'     => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
            'renderer' => [
                'custom' => 'Plugins/Blog/Backend/Post/Components/RatingColumn.twig',
            ],
        ],# rating
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
                'custom' => 'Plugins/Blog/Backend/Post/Components/Goto.twig',
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
                'custom' => 'Plugins/Blog/Backend/Post/Components/DeletePostNotice.twig',
            ],
        ],# notice
    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'language'    => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_blog_filter_post_language', 'default' => 'Select language'],
            'list'  => 'getSelectLanguages',
        ],
        'category'    => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_blog_filter_post_category', 'default' => 'Select category'],
            'list'  => 'getSelectCategories',
        ],
        'author'      => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_blog_filter_post_author', 'default' => 'Select author'],
            'list'  => 'getSelectBackendUsers',
        ],
        'headerTitle' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_blog_filter_post_headerTitle', 'default' => 'Search in header title'],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'content'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_blog_filter_post_content', 'default' => 'Search in content'],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'created' => CrudGroubByOrder::DESC,
    ];
    /** @var array $commentsPerPost */
    private $commentsPerPost;
    /** @var array $selectBackendUsers */
    private $selectBackendUsers;
    /** @var array $selectCategories */
    private $selectCategories;
    /** @var array $selectLanguages */
    private $selectLanguages;

    public function __construct() {
        parent::__construct();
    }

    /**
     * @inheritDoc
     * @throws ServiceNotFoundException
     * @throws ConfigElementNotFoundException
     */
    protected function modifyPropertyConfig(array $config) {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        if ($config['name'] === 'headerTitle') {
            $maxlength = $configService->get('blog_post_maxlength_header_title');
            if ($maxlength > 0) {
                $config['editor']['maxlength'] = $maxlength;
            }
        }

        return $config;
    }

    /** @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        $data = parent::prepareItemDataArray($entity, $crudAction);
        if ($crudAction !== 'create') {
            if (!isset($this->commentsPerPost)) {
                $this->commentsPerPost = [];
                /* @var EntityManager $entityManager */
                $entityManager = Oforge()->DB()->getEntityManager();
                $entries       = $entityManager->getRepository(Comment::class)->createQueryBuilder('c')#
                                               ->select('IDENTITY(c.post) as id, COUNT(c) as value')#
                                               ->groupBy('c.post')#
                                               ->getQuery()->getArrayResult();
                foreach ($entries as $entry) {
                    $this->commentsPerPost[$entry['id']] = $entry['value'];
                }
            }
            $data['comments'] = ArrayHelper::get($this->commentsPerPost, $data['id'], 0);

            /** @var DateTimeImmutable $dateTime */
            $dateTime        = $data['created'];
            $data['created'] = $dateTime->format('Y.m.d H:i:s');
            $dateTime        = $data['updated'];
            $data['updated'] = $dateTime->format('Y.m.d H:i:s');

            $data['author']   = $data['author']['id'];
            $data['category'] = $data['category']['id'];

            Oforge()->Services()->get('blog.rating')->evaluateRating($data);
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     */
    protected function convertData(array $data, string $crudAction) : array {
        /* @var EntityManager $entityManager */
        $entityManager = Oforge()->DB()->getEntityManager();

        $categoryID = $data['category'];
        /** @var Category|null $category */
        $category = $entityManager->getRepository(Category::class)->findOneBy(['id' => $categoryID]);
        if (!isset($category)) {
            throw new NotFoundException(sprintf(#
                I18N::translate('plugin_blog_category_not_found', 'Category with ID "%1" not found.'),#
                $categoryID)#
            );
        }
        $data['category'] = $category;
        $data['language'] = $category->getLanguage();
        if ($crudAction === 'create') {
            $data['author'] = Oforge()->View()->get('user.id');
        }

        return parent::convertData($data, $crudAction);
    }

    /**
     * Get BackendUsers for select field.
     *
     * @return array
     */
    protected function getSelectBackendUsers() {
        if (!isset($this->selectBackendUsers)) {
            $this->selectBackendUsers = [];
            /* @var EntityManager $entityManager */
            $entityManager = Oforge()->DB()->getEntityManager();
            /** @var BackendUser[] $entities */
            $entities = $entityManager->getRepository(BackendUser::class)->findBy(['active' => true]);
            foreach ($entities as $entity) {
                $this->selectBackendUsers[$entity->getId()] = $entity->getName();
            }
        }

        return $this->selectBackendUsers;
    }

    /**
     * Get categories for select field.
     *
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    protected function getSelectCategories() {
        if (!isset($this->selectCategories)) {
            $languages = $this->getSelectLanguages();
            /* @var EntityManager $entityManager */
            $entityManager      = Oforge()->DB()->getEntityManager();
            $criteria           = [];
            $filteredByLanguage = false;
            if (ArrayHelper::issetNotEmpty($_GET, 'language')) {
                $criteria['language'] = $_GET['language'];
                $filteredByLanguage   = true;
            }
            $categories = [];
            /** @var Category[] $entities */
            $entities = $entityManager->getRepository(Category::class)->findBy($criteria);
            foreach ($entities as $entity) {
                if ($filteredByLanguage) {
                    $categories[$entity->getId()] = $entity->getName();
                } else {
                    $language     = $entity->getLanguage();
                    $languageName = ArrayHelper::get($languages, $entity->getLanguage(), $entity->getLanguage());
                    if (!isset($categories[$language])) {
                        $categories[$language] = [
                            'label'   => $languageName,
                            'options' => [],
                        ];
                    }
                    $categories[$language]['options'][$entity->getId()] = $entity->getName();
                }
            }
            $this->selectCategories = $categories;
        }

        return $this->selectCategories;
    }

    /**
     * Get languages for select field.
     *
     * @return array
     * @throws ServiceNotFoundException
     * @throws ORMException
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
        // TODO: onPostDelete: handle comments, ratings
        return parent::handleDeleteAction($response, $entityID);
    }

}
