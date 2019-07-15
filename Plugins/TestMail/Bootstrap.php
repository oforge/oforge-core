<?php

namespace TestMail;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use TestMail\Controller\Backend\ShowMailController;
use TestMail\Controller\Backend\TestMailController;
use TestMail\Controller\Backend\SendMailController;


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
    public function activate() {

        $backendNavigationService = Oforge()->Services()->get('backend.navigation');

        $backendNavigationService->add([
            'name'     => 'backend_testmail',
            'order'    => 5,
            'parent'   => 'backend_content',
            'icon'     => 'fa fa-envelope',
            'path'     => 'backend_testmail',
            'position' => 'sidebar',
        ]);
    }
}
