<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:53
 */

namespace Oforge\Engine\Modules\CMS;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\CMS\ContentTypes\EntryList;
use Oforge\Engine\Modules\CMS\ContentTypes\Gallery;
use Oforge\Engine\Modules\CMS\ContentTypes\Image;
use Oforge\Engine\Modules\CMS\ContentTypes\NavigationEntry;
use Oforge\Engine\Modules\CMS\ContentTypes\RichText;
use Oforge\Engine\Modules\CMS\ContentTypes\Row;
use Oforge\Engine\Modules\CMS\ContentTypes\Text;
use Oforge\Engine\Modules\CMS\Controller\Backend\ElementsController;
use Oforge\Engine\Modules\CMS\Controller\Backend\PagesController;
use Oforge\Engine\Modules\CMS\Controller\Backend\TypesController;
use Oforge\Engine\Modules\CMS\Controller\Frontend\PageController;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentParent;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Layout\Slot;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\CMS\Models\Site\Site;
use Oforge\Engine\Modules\CMS\Services\ContentTypeGroupManagementService;
use Oforge\Engine\Modules\CMS\Services\ContentTypeManagementService;
use Oforge\Engine\Modules\CMS\Services\ContentTypeService;
use Oforge\Engine\Modules\CMS\Services\DummyPageGenerator;
use Oforge\Engine\Modules\CMS\Services\ElementsControllerService;
use Oforge\Engine\Modules\CMS\Services\ElementTreeService;
use Oforge\Engine\Modules\CMS\Services\NamedContentService;
use Oforge\Engine\Modules\CMS\Services\PageBuilderService;
use Oforge\Engine\Modules\CMS\Services\PagesControllerService;
use Oforge\Engine\Modules\CMS\Services\PageService;
use Oforge\Engine\Modules\CMS\Services\PageTreeService;
use Oforge\Engine\Modules\CMS\Twig\AccessExtension;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateRenderService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\CMS
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            \Oforge\Engine\Modules\Import\Bootstrap::class,
        ];

        $this->endpoints = [
            PagesController::class,
            TypesController::class,
            ElementsController::class,
            PageController::class,
        ];

        $this->models = [
            Site::class,
            Layout::class,
            Slot::class,
            ContentTypeGroup::class,
            ContentType::class,
            ContentParent::class,
            Content::class,
            \Oforge\Engine\Modules\CMS\Models\ContentTypes\Row::class,
            Page::class,
            PagePath::class,
            PageContent::class,
        ];

        $this->services = [

            'dummy.page.generator'          => DummyPageGenerator::class,
            'page.path'                     => PageService::class,
            'pages.controller.service'      => PagesControllerService::class,
            'page.tree.service'             => PageTreeService::class,
            'page.builder.service'          => PageBuilderService::class,
            'content.type.service'          => ContentTypeService::class,
            'elements.controller.service'   => ElementsControllerService::class,
            'element.tree.service'          => ElementTreeService::class,
            'named.content'                 => NamedContentService::class,
            'content.type.management'       => ContentTypeManagementService::class,
            'content.type.group.management' => ContentTypeGroupManagementService::class,
        ];
    }

    public function install() {
    }

    public function activate() {
        /**
         * @var BackendNavigationService $sidebarNavigation
         * @var ContentTypeGroupManagementService $contentTypeGroupManagementService
         * @var ContentTypeManagementService $managementService
         */
        $sidebarNavigation = Oforge()->Services()->get('backend.navigation');
        $sidebarNavigation->put([
            'name'     => 'backend_content',
            'order'    => 2,
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_content_pages',
            'order'    => 1,
            'parent'   => 'backend_content',
            'icon'     => 'fa fa-sitemap',
            'path'     => 'backend_content_pages',
            'position' => 'sidebar',
        ]);
        $sidebarNavigation->put([
            'name'     => 'backend_content_elements',
            'order'    => 1,
            'parent'   => 'backend_content',
            'icon'     => 'fa fa-folder',
            'path'     => 'backend_content_elements',
            'position' => 'sidebar',
        ]);

        $contentTypeGroupManagementService = Oforge()->Services()->get('content.type.group.management');
        $managementService                 = Oforge()->Services()->get('content.type.management');

        $ctgContainerID = $contentTypeGroupManagementService->put([
            'name'        => 'container',
            'description' => 'Container',
        ]);
        $managementService->put([
            'name'        => 'row',
            'path'        => 'Row',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/row.png',
            'description' => 'Row',
            'group'       => $ctgContainerID,
            'classPath'   => Row::class,
        ]);
        $managementService->put([
            'name'        => 'list',
            'path'        => 'List',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/row.png',
            'description' => 'List',
            'group'       => $ctgContainerID,
            'classPath'   => EntryList::class,
        ]);

        $ctgBasicID = $contentTypeGroupManagementService->put([
            'name'        => 'basic',
            'description' => 'Basic',
        ]);
        $managementService->put([
            'name'        => 'richtext',
            'path'        => 'RichText',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/richtext.png',
            'description' => 'RichText',
            'group'       => $ctgBasicID,
            'classPath'   => RichText::class,
        ]);
        $managementService->put([
            'name'        => 'text',
            'path'        => 'Text',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/text.png',
            'description' => 'Text',
            'group'       => $ctgBasicID,
            'classPath'   => Text::class,
        ]);

        $ctgMediaID = $contentTypeGroupManagementService->put([
            'name'        => 'media',
            'description' => 'Media',
        ]);
        $managementService->put([
            'name'        => 'image',
            'path'        => 'Image',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/image.png',
            'description' => 'Image',
            'group'       => $ctgMediaID,
            'classPath'   => Image::class,
        ]);
        $managementService->put([
            'name'        => 'gallery',
            'path'        => 'Gallery',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/gallery.png',
            'description' => 'Gallery',
            'group'       => $ctgMediaID,
            'classPath'   => Gallery::class,
        ]);

        $ctgNavigationID = $contentTypeGroupManagementService->put([
            'name'        => 'navigation',
            'description' => 'Navigation',
        ]);
        $managementService->put([
            'name'        => 'entrylist',
            'path'        => 'List',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/icontilebasic.png',
            'description' => 'entry_list',
            'group'       => $ctgNavigationID,
            'classPath'   => EntryList::class,
        ]);
        $managementService->put([
            'name'        => 'navigationentry',
            'path'        => 'NavigationEntry',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/icontilebasic.png',
            'description' => 'navigation_entry',
            'group'       => $ctgNavigationID,
            'classPath'   => NavigationEntry::class,
        ]);
    }

    public function load() {
        /** @var TemplateRenderService $templateRenderer */
        $templateRenderer = Oforge()->Services()->get('template.render');
        $templateRenderer->View()->addExtension(new AccessExtension());
    }

}
