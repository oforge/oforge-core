<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        /**
         * Events
         */
        override(
            \Oforge\Engine\Modules\Core\Manager\Events\Event::create(0),
            map(
                [
                    'mail.test.mailIds' => 'List of mail ids',
                    'mail.test.<id>'    => 'Mail config & testdata',
                ]
            )
        );
        override(
            \Oforge\Engine\Modules\Core\Manager\Events\EventManager::attach(0),
            map(
                [
                    'mail.test.mailIds' => 'List of mail ids',
                    'mail.test.<id>'    => 'Mail config & testdata',
                ]
            )
        );
    }
}
