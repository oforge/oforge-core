<?php

namespace Blog\Controller\Backend;

use Blog\Models\Comment;
use Blog\Services\CommentService;
use DateTimeImmutable;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Controller\Backend\BaseCrudController;
use Oforge\Engine\Modules\CRUD\Enum\CrudDataTypes;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterComparator;
use Oforge\Engine\Modules\CRUD\Enum\CrudFilterType;

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

    public function __construct() {
        parent::__construct();
    }

    /** @inheritDoc */
    protected function prepareItemDataArray(?AbstractModel $entity, string $crudAction) : array {
        if (!isset($entity)) {
            return [];
        }
        $data = $entity->toArray(1);
        if (!empty($data) && $crudAction !== 'create') {
            /** @var DateTimeImmutable $dateTime */
            $dateTime        = $data['created'];
            $data['created'] = $dateTime->format('Y.m.d H:i:s');
            $dateTime        = $data['updated'];
            $data['updated'] = $dateTime->format('Y.m.d H:i:s');

            $data['author'] = $data['author']['id'];
            $data['post']   = $data['post']['id'];
        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getSelectPosts() : array {
        try {
            /** @var CommentService $commentService */
            $commentService = Oforge()->Services()->get('blog.comment');

            return $commentService->getFilterDataPostsOfComments();
        } catch (ServiceNotFoundException $exception) {
            return [];
        }
    }

    /**
     * Get comment users for select field.
     *
     * @return array
     */
    protected function getSelectCommentUsers() : array {
        try {
            /** @var CommentService $commentService */
            $commentService = Oforge()->Services()->get('blog.comment');

            return $commentService->getFilterDataUserNamesOfComments();
        } catch (ServiceNotFoundException $exception) {
            return [];
        }
    }

}
