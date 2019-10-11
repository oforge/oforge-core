<?php

namespace ImageUpload;

use ImageUpload\Controller\Frontend\ImageUploadController;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->endpoints = [
            ImageUploadController::class
        ];

        $this->models = [];

        $this->services = [];
    }
}
