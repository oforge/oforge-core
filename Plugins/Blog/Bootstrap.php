<?php

namespace Blog;

use Blog\Models\Category;
use Blog\Models\Comment;
use Blog\Models\Post;
use Blog\Models\Rating;
use Blog\Services\UserService;
use Blog\Services\CategoryService;
use Blog\Services\CommentService;
use Blog\Services\PostService;
use Blog\Services\RatingService;
use Blog\Widgets\BlogOverviewWidget;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
            Controller\Backend\CategoryController::class,
            Controller\Backend\PostController::class,
            Controller\Backend\CommentController::class,
            Controller\Frontend\BlogController::class,
        ];

        $this->middlewares = [
            'frontend_blog' => [
                'class'    => Middlewares\BlogMiddleware::class,
                'position' => 1,
            ],
        ];

        $this->models = [
            Category::class,
            Post::class,
            Rating::class,
            Comment::class,
        ];

        $this->services = [
            'blog.user'     => UserService::class,
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
            'name'     => 'blog_load_more_epp_posts',
            'type'     => ConfigType::INTEGER,
            'group'    => 'blog',
            'default'  => 6,
            'label'    => 'config_blog_load_more_epp_posts',
            'required' => true,
        ]);
        $configService->add([
            'name'     => 'blog_load_more_epp_comments',
            'type'     => ConfigType::INTEGER,
            'group'    => 'blog',
            'default'  => 5,
            'label'    => 'config_blog_load_more_epp_comments',
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
        $configService->add([
            'name'    => 'blog_category_maxlength_name',
            'type'    => ConfigType::INTEGER,
            'group'   => 'blog',
            'default' => 0,
            'label'   => 'config_blog_category_maxlength_name',
        ]);
        $configService->add([
            'name'    => 'blog_category_icon_default',
            'type'    => ConfigType::STRING,
            'group'   => 'blog',
            'default' => '',
            'label'   => 'config_blog_category_icon_default',
        ]);
        $configService->add([
            'name'    => 'blog_post_maxlength_header_title',
            'type'    => ConfigType::INTEGER,
            'group'   => 'blog',
            'default' => 0,
            'label'   => 'config_blog_post_maxlength_header_title',
        ]);

        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->register([
            'position'     => 'top',
            'action'       => BlogOverviewWidget::class,
            'title'        => 'plugin_blog_dashboard_widget_overview_title',
            'name'         => 'plugin_blog_dashboard_widget_overview_name',
            'cssClass'     => 'bg-blue',
            'templateName' => 'BlogOverview',
        ]);

    }

    public function activate() {
        /** @var BackendNavigationService $sidebarNavigation */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'plugin_blog',
            'order'    => 3,
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'plugin_blog_categories',
            'order'    => 1,
            'parent'   => 'plugin_blog',
            'icon'     => 'fa fa-folder',
            'path'     => 'backend_blog_categories',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'plugin_blog_posts',
            'order'    => 2,
            'parent'   => 'plugin_blog',
            'icon'     => 'fa fa-sticky-note',
            'path'     => 'backend_blog_posts',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'plugin_blog_comments',
            'order'    => 3,
            'parent'   => 'plugin_blog',
            'icon'     => 'fa fa-comment',
            'path'     => 'backend_blog_comments',
            'position' => 'sidebar',
        ]);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function uninstall() {
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->unregister('blog_overview');
    }

}
