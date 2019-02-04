<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:53
 */

namespace Oforge\Engine\Modules\CMS;

use Oforge\Engine\Modules\CMS\Controller\Backend\ElementsController;
use Oforge\Engine\Modules\CMS\Controller\Backend\PagesController;
use Oforge\Engine\Modules\CMS\Controller\Backend\TypesController;
use Oforge\Engine\Modules\CMS\Controller\Frontend\PageController;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Layout\Slot;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\CMS\Models\Page\Site;
use Oforge\Engine\Modules\CMS\Services\DummyPageGenerator;
use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\CMS\Services\PageBuilderService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;


class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/backend/content/page" => [
                "controller" => PagesController::class,
                "name" => "backend_content_pages",
                "asset_scope" => "Backend",
            ],
            "/backend/content/types" => [
                "controller" => TypesController::class,
                "name" => "backend_content_types",
                "asset_scope" => "Backend"
            ],
            "/backend/types/elements" => [
                "controller" => ElementsController::class,
                "name" => "backend_content_elements",
                "asset_scope" => "Backend"
            ],
            "/[{content:.*}]" => [
                "controller" => PageController::class,
                "name" => "frontend_page",
                "asset_scope" => "Frontend",
                "order" => 99999
            ]
        ];

        $this->models = [
            Layout::class,
            Page::class,
            PagePath::class,
            Site::class,
            Slot::class,
            ContentTypeGroup::class,
            ContentType::class,
            Content::class,
            PageContent::class
        ];
        
        $this->dependencies = [
            \Oforge\Engine\Modules\Import\Bootstrap::class
        ];

        $this->services = [
            "pages.tree.view" => PageBuilderService::class,
            "page.path" => PageService::class,
            'dummy.page.generator' => DummyPageGenerator::class
        ];
    }
    
    public function install() {

    }


    public function activate()
    {
        $service = Oforge()->Services()->get("dummy.page.generator");
        $service->create();


        $sidebarNavigation = Oforge()->Services()->get("backend.navigation");

        $sidebarNavigation->put([
            "name" => "backend_content",
            "order" => 2,
        ]);

        $sidebarNavigation->put([
            "name" => "backend_content_pages",
            "order" => 1,
            "parent" => "backend_content",
            "icon" => "fa fa-sitemap",
            "path" => "backend_content_pages"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_content_elements",
            "order" => 1,
            "parent" => "backend_content",
            "icon" => "fa fa-folder",
            "path" => "backend_content_elements"
        ]);

        $sidebarNavigation->put([
            "name" => "backend_content_types",
            "order" => 1,
            "parent" => "backend_content",
            "icon" => "fa fa-cog",
            "path" => "backend_content_types"
        ]);

    }
}
