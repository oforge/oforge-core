<?php

namespace Oforge\Engine\Modules\Import;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Import\Services\ImportDatabaseCsvService;
use Oforge\Engine\Modules\Import\Services\ImportMediaService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Import
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->services = [
            'import.db_csv' => ImportDatabaseCsvService::class,
            'import.media'  => ImportMediaService::class,
        ];
    }

}
