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
use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\I18n\Models\Language;

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
            PagePath::class,
            Site::class,
            Slot::class,
            ContentType::class,
            Content::class,
            PageContent::class
        ];
        
        // $this->dependencies = [];
        $this->services = [
            "page.path" => PageService::class
        ];
    }
    
    public function install() {
        $em = Oforge()->DB()->getManager();
        
        $lang = Language::create(["iso" => "de", "name" => "de", "active" => 1]);
        $em->persist($lang);
        $em->flush($lang);
    
        $site = Site::create(["domain" => "www.oforge.com", "default_language" => 1]);
        $layout = Layout::create(["name" => "default"]);
    
        $em->persist($site);
        $em->persist($layout);
        
        $em->flush($site);
        $em->flush($layout);
        
        $repoSite = $em->getRepository(Site::class)->find(1);

        
        $repoLayout = $em->getRepository(Layout::class)->find(1);
        
        
    
        $page = Page::create(["name" => "Homepage", "layout" => $repoLayout->getId(), "site" => $repoSite->getId()]);
        $em->persist($page);
        $em->flush($page);
    
        $pagePath = PagePath::create([]);
        $pagePath->setPage($page);
        $pagePath->setLanguage(1);
        $pagePath->setPath("/blub");
        
        $em->persist($pagePath);
        $em->flush($pagePath);
    
    
        $contentType = ContentType::create([]);
        $contentType->setName("text");
        $contentType->setDescription("description");
        $contentType->setClassPath("asd");

    
        $em->persist($contentType);
        $em->flush($contentType);
    
    
        $content = Content::create([]);
        $content->setData("asdasd");
        $content->setType($contentType);
        $content->setName("test.text");
        $content->setParent(0);
        
        $em->persist($content);
        $em->flush($content);
        
        
        $pageContent = PageContent::create([]);
        $pageContent->setOrder(0)->setContent($content)->setPagePath($pagePath);
    
        $em->persist($pageContent);
        $em->flush($pageContent);
    
    }
}
