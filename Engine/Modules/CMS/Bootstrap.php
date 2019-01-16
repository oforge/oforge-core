<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:53
 */

namespace Oforge\Engine\Modules\CMS;

use Oforge\Engine\Modules\CMS\Controller\Frontend\PageController;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Layout\Slot;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageUrl;
use Oforge\Engine\Modules\CMS\Models\Page\Site;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/[{languageOrContent}[/{content}[/]]]" => [
                "controller" => PageController::class,
                "name" => "page",
                "asset_scope" => "Frontend",
                "order" => 99999
            ]
        ];

        $this->models = [
            Layout::class,
            Page::class,
            PageUrl::class,
            Site::class,
            Slot::class
        ];
        
        // $this->dependencies = [];
        
        // $this->services = [];
    }
}
