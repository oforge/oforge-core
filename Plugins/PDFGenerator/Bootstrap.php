<?php

namespace PDFGenerator;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use PDFGenerator\Controller\PDFTestController;
use PDFGenerator\Services\PDFGeneratorService;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->services = [
            "pdf" => PDFGeneratorService::class,
        ];
    }
}
