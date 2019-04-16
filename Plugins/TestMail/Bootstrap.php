<?php

namespace TestMail;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use TestMail\Controller\Frontend\TestMailController;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/testmail" => ["controller" => TestMailController::class, "name" => "testmail"],
        ];
    }
}