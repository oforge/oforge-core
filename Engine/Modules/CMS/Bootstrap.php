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
use Oforge\Engine\Modules\CMS\ContentTypes\VideoYoutube;
use Oforge\Engine\Modules\CMS\Controller\Backend\AjaxController;
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
use Oforge\Engine\Modules\CMS\Services\CmsOrderService;
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
            AjaxController::class,
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
            'cms.order'                     => CmsOrderService::class,
            'content.type.group.management' => ContentTypeGroupManagementService::class,
        ];
    }

    public function install() {
    }

    public function activate() {
        /**
         * @var BackendNavigationService $backendNavigationService
         * @var ContentTypeGroupManagementService $contentTypeGroupManagementService
         * @var ContentTypeManagementService $contentTypeManagementService
         */
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add([
            'name'     => 'backend_content_pages',
            'order'    => 1,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-sitemap',
            'path'     => 'backend_content_pages',
            'position' => 'sidebar',
        ]);
        $backendNavigationService->add([
            'name'     => 'backend_content_elements',
            'order'    => 2,
            'parent'   => BackendNavigationService::KEY_CONTENT,
            'icon'     => 'fa fa-folder',
            'path'     => 'backend_content_elements',
            'position' => 'sidebar',
        ]);

        $contentTypeGroupManagementService = Oforge()->Services()->get('content.type.group.management');
        $contentTypeManagementService      = Oforge()->Services()->get('content.type.management');

        $ctgContainerID = $contentTypeGroupManagementService->add([
            'name'  => 'container',
            'label' => [
                'en' => 'Container',
                'de' => 'Container',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'row',
            'path'      => 'Row',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/row.png',
            'group'     => $ctgContainerID,
            'classPath' => Row::class,
            'label'     => [
                'en' => 'Row',
                'de' => 'Zeile',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'list',
            'path'      => 'List',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/row.png',
            'group'     => $ctgContainerID,
            'classPath' => EntryList::class,
            'label'     => [
                'en' => 'List',
                'de' => 'Liste',
            ],
        ]);

        $ctgBasicID = $contentTypeGroupManagementService->add([
            'name'  => 'basic',
            'label' => [
                'en' => 'Basic',
                'de' => 'Basis',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'richtext',
            'path'      => 'RichText',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/richtext.png',
            'group'     => $ctgBasicID,
            'classPath' => RichText::class,
            'label'     => [
                'en' => 'RichText',
                'de' => 'RichText',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'text',
            'path'      => 'Text',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/text.png',
            'group'     => $ctgBasicID,
            'classPath' => Text::class,
            'label'     => [
                'en' => 'Text',
                'de' => 'Text',
            ],
        ]);

        $ctgMediaID = $contentTypeGroupManagementService->add([
            'name'  => 'media',
            'label' => [
                'en' => 'Media',
                'de' => 'Medien',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'image',
            'path'      => 'Image',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/image.png',
            'group'     => $ctgMediaID,
            'classPath' => Image::class,
            'label'     => [
                'en' => 'Image',
                'de' => 'Bild',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'gallery',
            'path'      => 'Gallery',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/gallery.png',
            'group'     => $ctgMediaID,
            'classPath' => Gallery::class,
            'label'     => [
                'en' => 'Gallery',
                'de' => 'Gallerie',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'        => 'video_youtube',
            'path'        => 'VideoYoutube',
            'icon'        => '/Themes/Base/ContentTypes/__assets/img/video_youtube.svg',
            'description' => 'Youtube video',
            'group'       => $ctgMediaID,
            'classPath'   => VideoYoutube::class,
            'label'       => [
                'en' => 'Youtube video',
                'de' => 'Youtube-Video',
            ],
        ]);

        $ctgNavigationID = $contentTypeGroupManagementService->add([
            'name'  => 'navigation',
            'label' => [
                'en' => 'Navigation',
                'de' => 'Navigation',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'entry_list',
            'path'      => 'List',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/icontilebasic.png',
            'group'     => $ctgNavigationID,
            'classPath' => EntryList::class,
            'label'     => [
                'en' => 'Navigation List',
                'de' => 'Navigationsliste',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'navigation_entry',
            'path'      => 'NavigationEntry',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/icontilebasic.png',
            'group'     => $ctgNavigationID,
            'classPath' => NavigationEntry::class,
            'label'     => [
                'en' => 'Navigation element',
                'de' => 'Navigationselement',
            ],
        ]);
    }

    public function load() {
        /** @var TemplateRenderService $templateRenderer */
        $templateRenderer = Oforge()->Services()->get('template.render');
        $templateRenderer->View()->addExtension(new AccessExtension());
    }

}
