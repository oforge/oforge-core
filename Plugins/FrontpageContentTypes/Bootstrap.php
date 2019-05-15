<?php


namespace FrontpageContentTypes;


use FrontpageContentTypes\ContentTypes\IconTileBasic;
use FrontpageContentTypes\Services\RegisterContentTypeService;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->models = [
            ContentType::class,
            ContentTypeGroup::class
        ];
        $this->services = [
            "frontpage.content.types.register.contenttype" => RegisterContentTypeService::class
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function activate()
    {
        /** @var RegisterContentTypeService $registerContentTypeService */
        $registerContentTypeService = Oforge()->Services()->get("frontpage.content.types.register.contenttype");

        $registerContentTypeService->registerContentType('basic',
            'icontilebasic',
            'IconTileBasic',
            '/Themes/Base/ContentTypes/__assets/img/icontilebasic.jpg',
            'Icon Tile Basic',
            'FrontpageContentTypes\\ContentTypes\\IconTileBasic');
    }
}