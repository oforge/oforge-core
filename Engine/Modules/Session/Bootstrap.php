<?php

namespace Oforge\Engine\Modules\Session;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Session\Middleware\SessionMiddleware;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Session
 */
class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->middleware = [
            '*' => ['class' => SessionMiddleware::class, 'position' => 999999],
        ];
        $this->services   = [
            'session.management' => SessionManagementService::class,
        ];
        $this->order      = 2;
    }
}
