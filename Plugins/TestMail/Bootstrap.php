<?php

namespace TestMail;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use TestMail\Controller\Frontend\ShowMailController;
use TestMail\Controller\Frontend\TestMailController;
use TestMail\Controller\Frontend\SendMailController;

/**
 * Class Bootstrap
 *
 * @package TestMail
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            TestMailController::class,
            ShowMailController::class,
            SendMailController::class,
        ];
    }

}
