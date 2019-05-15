<?php

namespace Blog;

use Blog\Models\Category;
use Blog\Models\Comment;
use Blog\Models\Post;
use Blog\Models\Rating;
use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Blog
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            // \Oforge\Engine\Modules\CRUD\Bootstrap::class,
            \FrontendUserManagement\Bootstrap::class,
        ];

        $this->endpoints = [
            // CategoryController::class,
            // PostController::class,
            // CommentController::class,
            // BlogController::class,
        ];

        $this->models = [
            Category::class,
            Post::class,
            Rating::class,
            Comment::class,
        ];

        $this->services = [
            'blog.category' => CategoryService::class,
            'blog.comment'  => CommentService::class,
            'blog.post'     => PostService::class,
            'blog.rating'   => RatingService::class,
        ];
    }

    public function install() {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add([
            'name'     => 'blog_epp_posts',
            'type'     => ConfigType::INTEGER,
            'group'    => 'blog',
            'default'  => 6,
            'label'    => 'config_blog_epp_posts',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'blog_epp_comments',
            'type'     => ConfigType::INTEGER,
            'group'    => 'blog',
            'default'  => 5,
            'label'    => 'config_blog_epp_comments',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'blog_recommend_posts_number',
            'type'     => ConfigType::INTEGER,
            'group'    => 'blog',
            'default'  => 3,
            'label'    => 'config_blog_recommend_posts_number',
            'required' => true,
        ]);
    }

}
