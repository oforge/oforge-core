<?php

namespace Blog\Controller\Backend;

use Blog\Exceptions\PostNotFoundException;
use Blog\Models\Category;
use Blog\Models\Post;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
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
            'type'  => CrudDataTypes::DATETIME,
            'label' => [
                'key'     => 'plugin_blog_property_post_created',
                'default' => [
                    'en' => 'Created',
                    'de' => 'Erstellt',
                ],
            ],
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
            'type'  => CrudDataTypes::DATETIME,
            'label' => [
                'key'     => 'plugin_blog_property_post_updated',
                'default' => [
                    'en' => 'Updated',
                    'de' => 'Aktualisiert',
                ],
            ],
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
            'label' => [
                'key'     => 'plugin_blog_property_post_language',
                'default' => [
                    'en' => 'Language',
                    'de' => 'Sprache',
                ],
            ],
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
            'label' => [
                'key'     => 'plugin_blog_property_post_category',
                'default' => [
                    'en' => 'Category',
                    'de' => 'Kategorie',
                ],
            ],
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
            'label' => [
                'key'     => 'plugin_blog_property_post_seoUrlPath',
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
            'name'   => 'headerTitle',
            'type'   => CrudDataTypes::STRING,
            'label'  => [
                'key'     => 'plugin_blog_property_post_headerTitle',
                'default' => [
                    'en' => 'Header title',
                    'de' => 'Überschrift-Titel',
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
        ],# headerTitle
        [
            'name'  => 'headerSubtext',
            'type'  => CrudDataTypes::HTML,
            'label' => [
                'key'     => 'plugin_blog_property_post_headerSubtext',
                'default' => [
                    'en' => 'Header subtext',
                    'de' => 'Überschrift-Subtext',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# headerSubtext
        [
            'name'  => 'headerImagePreview',
            'type'  => CrudDataTypes::IMAGE,
            'label' => [
                'key'     => 'plugin_blog_property_post_headerImagePreview',
                'default' => [
                    'en' => 'Header image (small preview)',
                    'de' => 'Kopfbild (kleine Vorschau)',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# headerImagePreview
        [
            'name'  => 'headerImageLarge',
            'type'  => CrudDataTypes::IMAGE,
            'label' => [
                'key'     => 'plugin_blog_property_post_headerImageLarge',
                'default' => [
                    'en' => 'Header image (full width)',
                    'de' => 'Kopfbild (volle Breite)',
                ],
            ],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'off',
            ],
        ],# headerImageLarge
        [
            'name'  => 'excerpt',
            'type'  => CrudDataTypes::TEXT,
            'label' => [
                'key'     => 'plugin_blog_property_post_excerpt',
                'default' => [
                    'en' => 'Excerpt',
                    'de' => 'Auszug',
                ],
            ],
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
            'label' => [
                'key'     => 'plugin_blog_property_post_content',
                'default' => [
                    'en' => 'Content',
                    'de' => 'Inhalt',
                ],
            ],
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
            'label' => [
                'key'     => 'plugin_blog_property_post_author',
                'default' => [
                    'en' => 'Author',
                    'de' => 'Autor',
                ],
            ],
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
            'name'   => 'active',
            'type'   => CrudDataTypes::BOOL,
            'label'  => [
                'key'     => 'plugin_blog_property_post__active',
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
            'name'     => 'rating',
            'type'     => CrudDataTypes::CUSTOM,
            'label'    => [
                'key'     => 'plugin_blog_property_post_rating',
                'default' => [
                    'en' => 'Rating',
                    'de' => 'Bewertung',
                ],
            ],
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
                'custom' => 'Plugins/Blog/Backend/Post/Components/Goto.twig',
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
                'custom' => 'Plugins/Blog/Backend/Post/Components/DeletePostNotice.twig',
            ],
        ],# notice
    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'language'    => [
            'type'  => CrudFilterType::SELECT,
            'label' => [
                'key'     => 'plugin_blog_filter_post_language',
                'default' => [
                    'en' => 'Select language',
                    'de' => 'Sprache auswählen',
                ],
            ],
            'list'  => 'getSelectLanguages',
        ],
        'category'    => [
            'type'  => CrudFilterType::SELECT,
            'label' => [
                'key'     => 'plugin_blog_filter_post_category',
                'default' => [
                    'en' => 'Select category',
                    'de' => 'Kategorie auswählen',
                ],
            ],
            'list'  => 'getSelectCategories',
        ],
        'author'      => [
            'type'  => CrudFilterType::SELECT,
            'label' => [
                'key'     => 'plugin_blog_filter_post_author',
                'default' => [
                    'en' => 'Select author',
                    'de' => 'Autor auswählen',
                ],
            ],
            'list'  => 'getSelectBackendUsers',
        ],
        'headerTitle' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'plugin_blog_filter_post_headerTitle',
                'default' => [
                    'en' => 'Search in header title',
                    'de' => 'Im Überschrift-Titel suchen',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
        'content'     => [
            'type'    => CrudFilterType::TEXT,
            'label'   => [
                'key'     => 'plugin_blog_filter_post_content',
                'default' => [
                    'en' => 'Search in content',
                    'de' => 'Im Inhalt suchen',
                ],
            ],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var array $indexOrderBy */
    protected $indexOrderBy = [
        'created' => CrudGroubByOrder::DESC,
    ];

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
        if (!empty($data) && $crudAction !== 'create') {
            if (!isset($this->filterSelectData['commentsPerPost'])) {
                try {
                    /** @var PostService $postService */
                    $postService = Oforge()->Services()->get('blog.post');

                    $this->filterSelectData['commentsPerPost'] = $postService->getFilterDataCommentsCountOfPosts();
                } catch (ServiceNotFoundException $exception) {
                    $this->filterSelectData['commentsPerPost'] = [];
                }
            }
            $data['comments'] = ArrayHelper::get($this->filterSelectData['commentsPerPost'], $data['id'], 0);

            $data['author']   = $data['author']['id'];
            $data['category'] = $data['category']['id'];

            /** @var RatingService $ratingService */
            $ratingService = Oforge()->Services()->get('blog.rating');
            $ratingService->evaluateRating($data);
        }

        return $data;
    }

    /**
     * @inheritDoc
     * @throws NotFoundException
     */
    protected function convertData(array $data, string $crudAction) : array {
        /* @var ForgeEntityManager $entityManager */
        $entityManager = Oforge()->DB()->getForgeEntityManager();

        $categoryID = $data['category'];
        /** @var Category|null $category */
        $category = $entityManager->getRepository(Category::class)->findOneBy(['id' => $categoryID]);
        if (!isset($category)) {
            throw new NotFoundException(sprintf(#
                I18N::translate('plugin_blog_category_not_found', [
                    'en' => 'Category with ID "%s" not found.',
                    'de' => 'Kategorie mit der ID "%s" wurde nicht gefunden.',
                ]),#
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
    protected function getSelectBackendUsers() : array {
        try {
            /** @var PostService $postService */
            $postService = Oforge()->Services()->get('blog.post');

            return $postService->getFilterDataUsersOfPosts();
        } catch (ServiceNotFoundException $exception) {
            return [];
        }
    }

    /**
     * Get categories for select field.
     *
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    protected function getSelectCategories() : array {
        try {
            /** @var PostService $postService */
            $postService = Oforge()->Services()->get('blog.post');

            return $postService->getFilterDataCategoriesOfPosts();
        } catch (ServiceNotFoundException $exception) {
            return [];
        }
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
        /** @var PostService $postService */
        $postService = Oforge()->Services()->get('blog.post');
        try {
            $result = $postService->delete($entityID);
            if (isset($result)) {
                list($commentsDeleted, $ratingsDeleted) = $result;
                Oforge()->View()->Flash()->addMessage('success', sprintf(#
                    I18N::translate('plugin_blog_msg_post_delete_success', [
                        'en' => 'Post with %s comments & %s ratings successfully deleted.',
                        'de' => 'Beitrag mit %s Kommentaren & %s Bewertungen erfolgreich gelöscht.',
                    ]),#
                    $commentsDeleted,#
                    $ratingsDeleted#
                ));
            } else {
                Oforge()->View()->Flash()->addMessage('error', I18N::translate('backend_crud_msg_delete_failed', [
                    'en' => 'Entity deletion failed.',
                    'de' => 'Löschen des Elements fehlgeschlagen.',
                ]));
            }
        } catch (PostNotFoundException $exception) {
            Oforge()->View()->Flash()->addMessage('error', sprintf(#
                I18N::translate('plugin_blog_post_not_found', [
                    'en' => 'Post with ID "%s" not found.',
                    'de' => 'Beitrag mit ID "%s" nicht gefunden.',
                ]),#
                $entityID#
            ));
        }

        return $this->redirect($response, 'index');
    }

}
