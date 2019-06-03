<?php

namespace Blog\Controller\Backend;

use Blog\Models\Comment;
use Blog\Models\Post;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\UserDetail;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Services\LanguageService;

/**
 * Class CommentController
 *
 * @package Blog\Controller\Backend\Blog
 * @EndpointClass(path="/backend/blog/comments", name="backend_blog_comments", assetScope="Backend")
 */
class CommentController extends BaseCrudController {
    /** @var string $model */
    protected $model = Comment::class;
    /** @var array $modelProperties */
    protected $modelProperties = [
        [
            'name'  => 'created',
            'type'  => CrudDataTypes::STRING,
            'label' => ['key' => 'plugin_blog_property_comment_created', 'default' => 'Created'],
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
            'label' => ['key' => 'plugin_blog_property_comment_updated', 'default' => 'Updated'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
        ],# updated
        [
            'name'  => 'post',
            'type'  => CrudDataTypes::SELECT,
            'label' => ['key' => 'plugin_blog_property_comment_post', 'default' => 'Post'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectPosts',
        ],# author
        [
            'name'  => 'author',
            'type'  => CrudDataTypes::SELECT,
            'label' => ['key' => 'plugin_blog_property_comment_author', 'default' => 'Author'],
            'crud'  => [
                'index'  => 'readonly',
                'view'   => 'readonly',
                'create' => 'off',
                'update' => 'readonly',
                'delete' => 'readonly',
            ],
            'list'  => 'getSelectCommentUsers',
        ],# author
        [
            'name'  => 'content',
            'type'  => CrudDataTypes::HTML,
            'label' => ['key' => 'plugin_blog_property_comment_content', 'default' => 'Content'],
            'crud'  => [
                'index'  => 'off',
                'view'   => 'readonly',
                'create' => 'editable',
                'update' => 'editable',
                'delete' => 'readonly',
            ],
        ],# content
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
                'custom' => 'Plugins/Blog/Backend/Comment/Components/DeleteCommentNotice.twig',
            ],
        ],# notice

    ];
    /** @var array $indexFilter */
    protected $indexFilter = [
        'post'    => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_blog_filter_comment_post', 'default' => 'Select post'],
            'list'  => 'getSelectPosts',
        ],
        'author'  => [
            'type'  => CrudFilterType::SELECT,
            'label' => ['key' => 'plugin_blog_filter_comment_author', 'default' => 'Select author'],
            'list'  => 'getSelectCommentUsers',
        ],
        'content' => [
            'type'    => CrudFilterType::TEXT,
            'label'   => ['key' => 'plugin_blog_filter_comment_content', 'default' => 'Search in content'],
            'compare' => CrudFilterComparator::LIKE,
        ],
    ];
    /** @var array $crudActions */
    protected $crudActions = [
        'index'  => true,
        'create' => false,
        'view'   => true,
        'update' => true,
        'delete' => true,
    ];
    /** @var array $selectCommentUsers */
    private $selectCommentUsers;
    /** @var array $selectPosts */
    private $selectPosts;

    public function __construct() {
        parent::__construct();
    }

    /** @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        $data = $entity->toArray(1);
        if ($crudAction !== 'create') {
            /** @var DateTimeImmutable $dateTime */
            $dateTime        = $data['created'];
            $data['created'] = $dateTime->format('Y.m.d H:i:s');
            $dateTime        = $data['updated'];
            $data['updated'] = $dateTime->format('Y.m.d H:i:s');

            $data['author'] = $data['author']['id'];
            $data['post'] = $data['post']['id'];
        }

        return $data;
    }

    /**
     * @return array
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    protected function getSelectPosts() {
        if (!isset($this->selectPosts)) {
            /* @var EntityManager $entityManager */
            $entityManager = Oforge()->DB()->getEntityManager();
            $languages     = [];
            /** @var LanguageService $languageService */
            $languageService = Oforge()->Services()->get('i18n.language');
            /** @var Language[] $entities */
            $entities = $languageService->list();
            foreach ($entities as $entity) {
                $languages[$entity->getIso()] = $entity->getName();
            }
            $queryBuilder = $entityManager->getRepository(Post::class)->createQueryBuilder('p')#
                                          ->select('p')#
                                          ->leftJoin('p.comments', 'c')#
                                          ->groupBy('p.id')#
                                          ->distinct();
            if (ArrayHelper::issetNotEmpty($_GET, 'author')) {
                $queryBuilder = $queryBuilder->where('c.author = ?1')->setParameter(1, $_GET['author']);
            }
            /** @var Post[] $entities */
            $entities = $queryBuilder->getQuery()->getResult();

            $posts = [];
            foreach ($entities as $entity) {
                $language     = $entity->getLanguage();
                $languageName = ArrayHelper::get($languages, $entity->getLanguage(), $entity->getLanguage());
                if (!isset($posts[$language])) {
                    $posts[$language] = [
                        'label'   => $languageName,
                        'options' => [],
                    ];
                }
                $posts[$language]['options'][$entity->getId()] = $entity->getHeaderTitle();
            }
            $this->selectPosts = $posts;
        }

        return $this->selectPosts;
    }

    /**
     * Get comment users for select field.
     *
     * @return array
     * @return array
     */
    protected function getSelectCommentUsers() {
        if (!isset($this->selectCommentUsers)) {
            $this->selectCommentUsers = [];
            /* @var EntityManager $entityManager */
            $entityManager = Oforge()->DB()->getEntityManager();

            $queryBuilder = $entityManager->getRepository(Comment::class)->createQueryBuilder('c')#
                                          ->select('ud')#
                                          ->leftJoin('c.author', 'fu')#
                                          ->leftJoin(UserDetail::class, 'ud', 'WITH', 'ud.user = fu.id')#
                                          ->groupBy('ud.id')#
                                          ->distinct();
            if (ArrayHelper::issetNotEmpty($_GET, 'post')) {
                $queryBuilder = $queryBuilder->where('c.post = ?1')->setParameter(1, $_GET['post']);
            }
            /** @var UserDetail[] $userDetails */
            $userDetails = $queryBuilder->getQuery()->getResult();
            foreach ($userDetails as $userDetail) {
                $this->selectCommentUsers[$userDetail->getId()] = $userDetail->getLastName() . ', ' . $userDetail->getFirstName();
            }
        }

        return $this->selectCommentUsers;
    }

}
