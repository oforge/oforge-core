<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 12.12.2018
 * Time: 12:29
 */

namespace Oforge\Engine\Modules\Session;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Session\Middleware\SessionMiddleware;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;

/**
 * Class Bootstrap
 * @package Oforge\Engine\Modules\Session
 */
class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        
        $this->services = [
            "session.management" => SessionManagementService::class
        ];
        $this->middleware = [
            "*" => ["class" => SessionMiddleware::class, "position" => 2]
        ];
        $this->order = 2;
    }
}
