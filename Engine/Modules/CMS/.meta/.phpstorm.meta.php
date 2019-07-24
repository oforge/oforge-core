<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'cms.order'                     => \Oforge\Engine\Modules\CMS\Services\CmsOrderService::class,
            'dummy.page.generator'          => \Oforge\Engine\Modules\CMS\Services\DummyPageGenerator::class,
            'page.path'                     => \Oforge\Engine\Modules\CMS\Services\PageService::class,
            'pages.controller.service'      => \Oforge\Engine\Modules\CMS\Services\PagesControllerService::class,
            'page.tree.service'             => \Oforge\Engine\Modules\CMS\Services\PageTreeService::class,
            'page.builder.service'          => \Oforge\Engine\Modules\CMS\Services\PageBuilderService::class,
            'content.type.service'          => \Oforge\Engine\Modules\CMS\Services\ContentTypeService::class,
            'elements.controller.service'   => \Oforge\Engine\Modules\CMS\Services\ElementsControllerService::class,
            'element.tree.service'          => \Oforge\Engine\Modules\CMS\Services\ElementTreeService::class,
            'named.content'                 => \Oforge\Engine\Modules\CMS\Services\NamedContentService::class,
            'content.type.management'       => \Oforge\Engine\Modules\CMS\Services\ContentTypeManagementService::class,
            'content.type.group.management' => \Oforge\Engine\Modules\CMS\Services\ContentTypeGroupManagementService::class,
        ]));
    }

}
