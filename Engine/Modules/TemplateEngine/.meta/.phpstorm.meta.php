<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'url' => \Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService::class,
        ]));
    }

}
