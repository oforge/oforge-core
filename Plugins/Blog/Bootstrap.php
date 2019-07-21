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
use Blog\Widgets\BlogOverviewWidget;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\AdminBackend\Core\Services\DashboardWidgetsService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;

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
            'blog.category' => CategoryService::class,
            'blog.comment'  => CommentService::class,
            'blog.post'     => PostService::class,
            'blog.rating'   => RatingService::class,
        ];
    }

    public function install() {
        I18N::translate('config_blog_load_more_epp_posts', ['en' => 'Posts per load more page', 'de' => 'Beiträge pro weiterer Seite']);
        I18N::translate('config_blog_load_more_epp_comments', ['en' => 'Comments per load more page', 'de' => 'Kommentare pro weiterer Seite']);
        I18N::translate('config_blog_recommend_posts_number', ['en' => 'Number of recommended posts', 'de' => 'Anzahl empfohlener Beiträge']);
        I18N::translate('config_blog_category_maxlength_name', ['en' => 'Max length of category name', 'de' => 'Maximale Länge des Kategorienamens']);
        I18N::translate('config_blog_category_icon_default', ['en' => 'Default category icon', 'de' => 'Standard Kategorie-Icon']);
        I18N::translate('config_blog_post_maxlength_header_title', ['en' => 'Post header title max length', 'de' => 'Maximale Länge des Beitrags-Kopf-Titels']);
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

        I18N::translate('plugin_blog_dashboard_widget_overview_title', ['en' => 'Blog statistics', 'de' => 'Blog-Statistiken']);
        /** @var DashboardWidgetsService $dashboardWidgetsService */
        $dashboardWidgetsService = Oforge()->Services()->get('backend.dashboard.widgets');
        $dashboardWidgetsService->register([
            'position'     => 'right',
            'action'       => BlogOverviewWidget::class,
            'title'        => 'plugin_blog_dashboard_widget_overview_title',
            'name'         => 'plugin_blog_dashboard_widget_overview_name',
            'cssClass'     => 'box-blue',
            'templateName' => 'BlogOverview',
        ]);

    }

    public function activate() {
        I18N::translate('plugin_blog', ['en' => 'Blog', 'de' => 'Blog']);
        I18N::translate('plugin_blog_categories', ['en' => 'Categories', 'de' => 'Kategorien']);
        I18N::translate('plugin_blog_posts', ['en' => 'Posts', 'de' => 'Beiträge']);
        I18N::translate('plugin_blog_comments', ['en' => 'Comments', 'de' => 'Kommentare']);
        /** @var BackendNavigationService $backendNavigationService */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add([
            'name'     => 'plugin_blog',
            'order'    => 3,
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'plugin_blog_categories',
            'order'    => 1,
            'parent'   => 'plugin_blog',
            'icon'     => 'fa fa-folder',
            'path'     => 'backend_blog_categories',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'plugin_blog_posts',
            'order'    => 2,
            'parent'   => 'plugin_blog',
            'icon'     => 'fa fa-sticky-note',
            'path'     => 'backend_blog_posts',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
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
