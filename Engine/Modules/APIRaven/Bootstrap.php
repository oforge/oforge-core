<?php

namespace Oforge\Engine\Modules\APIRaven;

use Oforge\Engine\Modules\APIRaven\Services\APIRavenService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap
{
    /**
     * Bootstrap constructor.
     */
    public function __construct()
    {
        $this->services = [
            "apiraven" => APIRavenService::class
        ];
    }
}