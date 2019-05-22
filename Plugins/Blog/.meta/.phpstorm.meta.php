<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'blog.auth'     => \Blog\Services\AuthService::class,
            'blog.category' => \Blog\Services\CategoryService::class,
            'blog.comment'  => \Blog\Services\CommentService::class,
            'blog.post'     => \Blog\Services\PostService::class,
            'blog.rating'   => \Blog\Services\RatingService::class,
        ]));
    }

}
