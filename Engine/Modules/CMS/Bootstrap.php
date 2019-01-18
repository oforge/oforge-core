<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:53
 */

namespace Oforge\Engine\Modules\CMS;
use Oforge\Engine\Modules\CMS\Controller\Frontend\PageController;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Layout\Slot;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\CMS\Models\Page\Site;
use Oforge\Engine\Modules\CMS\Services\DummyPageGenerator;
use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\I18n\Models\Language;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/[{content:.*}]" => [
                "controller" => PageController::class,
                "name" => "page",
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
            ContentType::class,
            Content::class,
            PageContent::class
        ];
        
        // $this->dependencies = [];
        $this->services = [
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

    }
}
