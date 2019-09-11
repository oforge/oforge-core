<?php

namespace VideoUpload;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use VideoUpload\Controller\Frontend\VideoUploadController;

/**
 * Class Bootstrap
 *
 * @package VideoUpload
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            \Insertion\Bootstrap::class,
        ];

        $this->endpoints = [
            VideoUploadController::class,
        ];

        $this->models = [];

        $this->services = [];
    }
}
