<?php

namespace FrontpageContentTypes;

use FrontpageContentTypes\ContentTypes\Customers;
use FrontpageContentTypes\ContentTypes\FullWidthSlider;
use FrontpageContentTypes\ContentTypes\IconTileBasic;
use FrontpageContentTypes\ContentTypes\IconTileText;
use FrontpageContentTypes\ContentTypes\ImageTileLarge;
use FrontpageContentTypes\ContentTypes\ImageTileMedium;
use FrontpageContentTypes\ContentTypes\ImageTileSmall;
use FrontpageContentTypes\ContentTypes\TextIconPrefix;
use Oforge\Engine\Modules\CMS\Services\ContentTypeGroupManagementService;
use Oforge\Engine\Modules\CMS\Services\ContentTypeManagementService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package FrontpageContentTypes
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
    }

    public function activate() {
        /**
         * @var ContentTypeGroupManagementService $contentTypeGroupManagementService
         * @var ContentTypeManagementService $contentTypeManagementService
         */
        $contentTypeGroupManagementService = Oforge()->Services()->get('content.type.group.management');
        $contentTypeManagementService      = Oforge()->Services()->get('content.type.management');

        $ctgTilesID = $contentTypeGroupManagementService->add([
            'name'  => 'tiles',
            'label' => [
                'en' => 'Tiles',
                'de' => 'Kacheln',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'icontilebasic',
            'path'      => 'IconTileBasic',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/icontilebasic.png',
            'group'     => $ctgTilesID,
            'classPath' => IconTileBasic::class,
            'label'     => [
                'en' => 'Icon tile basic',
                'de' => 'Basis Icon-Kachel',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'icontiletext',
            'path'      => 'IconTileText',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/icontilebasic.png',
            'group'     => $ctgTilesID,
            'classPath' => IconTileText::class,
            'label'     => [
                'en' => 'Icon tile text',
                'de' => 'Text-Icon-Kachel',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'imagetilesmall',
            'path'      => 'ImageTileSmall',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/imagetile.png',
            'group'     => $ctgTilesID,
            'classPath' => ImageTileSmall::class,
            'label'     => [
                'en' => 'Icon tile small',
                'de' => 'Kleine Icon-Kachel',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'imagetilemedium',
            'path'      => 'ImageTileMedium',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/imagetile.png',
            'group'     => $ctgTilesID,
            'classPath' => ImageTileMedium::class,
            'label'     => [
                'en' => 'Icon tile medium',
                'de' => 'Medium Icon-Kachel',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'imagetilelarge',
            'path'      => 'ImageTileLarge',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/imagetile.png',
            'group'     => $ctgTilesID,
            'classPath' => ImageTileLarge::class,
            'label'     => [
                'en' => 'Icon tile large',
                'de' => 'Große Icon-Kachel',
            ],
        ]);

        $ctgOtherID = $contentTypeGroupManagementService->add([
            'name'  => 'other',
            'label' => [
                'en' => 'Others',
                'de' => 'Sonstiges',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'texticonprefix',
            'path'      => 'TextIconPrefix',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/imagetile.png',
            'group'     => $ctgOtherID,
            'classPath' => TextIconPrefix::class,
            'label'     => [
                'en' => 'Text with icon prefix',
                'de' => 'Text mit Iconpräfix',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'fullwidthslider',
            'path'      => 'FullWidthSlider',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/imagetile.png',
            'group'     => $ctgOtherID,
            'classPath' => FullWidthSlider::class,
            'label'     => [
                'en' => 'Full width slider',
                'de' => 'Slider in voller Breite',
            ],
        ]);
        $contentTypeManagementService->add([
            'name'      => 'customers',
            'path'      => 'Customers',
            'icon'      => '/Themes/Base/ContentTypes/__assets/img/imagetile.png',
            'group'     => $ctgOtherID,
            'classPath' => Customers::class,
            'label'     => [
                'en' => 'Customers',
                'de' => 'Kunden',
            ],
        ]);
    }

}
