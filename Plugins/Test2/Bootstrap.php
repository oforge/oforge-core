<?php

namespace Test2;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap
{
    public function __construct() {
        $this->dependencies = [];
    }
}
