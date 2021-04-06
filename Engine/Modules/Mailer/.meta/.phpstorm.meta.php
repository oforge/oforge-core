<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Services
         */
        override(
            \Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0),
            map(
                [
                    'mail'           => \Oforge\Engine\Modules\Mailer\MailService::class,
                    // 'mail.list'      => \Oforge\Engine\Modules\Mailer\MailingListService::class,
                    'mail.inlineCss' => \Oforge\Engine\Modules\Mailer\InlineCssService::class,
                ]
            )
        );
    }
}
