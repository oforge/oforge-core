<?php

namespace Oforge\Engine\Modules\CRUD;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class Bootstrap extends AbstractBootstrap
{
    function __construct()
    {
        $this->services = [
          "crud" => GenericCrudService::class
        ];
    }
}