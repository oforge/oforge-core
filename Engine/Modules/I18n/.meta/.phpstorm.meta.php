<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'i18n'          => \Oforge\Engine\Modules\I18n\Services\InternationalizationService::class,
            'i18n.language' => \Oforge\Engine\Modules\I18n\Services\LanguageService::class,
        ]));
    }

}
