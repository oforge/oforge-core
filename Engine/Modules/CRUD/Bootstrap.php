<?php

namespace Oforge\Engine\Modules\CRUD;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\CRUD
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->services = [
            'crud' => GenericCrudService::class,
        ];
    }

}
