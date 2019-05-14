<?php

namespace Blog;

use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Blog
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            \Oforge\Engine\Modules\CRUD\Bootstrap::class,
        ];

        $this->endpoints = [
            // CategoryController::class,
            // PostController::class,
            // CommentController::class,
            // BlogController::class,
        ];

        $this->models = [

        ];

        $this->services = [
            'blog.category' => CategoryService::class,
            'blog.comment'  => CommentService::class,
            'blog.post'     => PostService::class,
        ];
    }

}
