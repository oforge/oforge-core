<?php

namespace PHPSTORM_META {

    if (function_exists('override')) {
        override(\Oforge\Engine\Modules\Core\Manager\Services\ServiceManager::get(0), map([
            'import.db_csv' => \Oforge\Engine\Modules\Import\ImportDatabaseCsvService::class,
            'import.media'  => \Oforge\Engine\Modules\Import\ImportMediaService::class,
        ]));
    }

}
