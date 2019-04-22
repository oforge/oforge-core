<?php

namespace TestMail;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use TestMail\Controller\Frontend\TestMailController;

/**
 * Class Bootstrap
 *
 * @package TestMail
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            TestMailController::class,
        ];
    }

}
