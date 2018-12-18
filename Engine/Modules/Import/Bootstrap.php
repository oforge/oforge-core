<?php

namespace Oforge\Engine\Modules\Import;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Import\Services\ImportService;


class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->services = [
            "import" => ImportService::class
        ];
    }
}