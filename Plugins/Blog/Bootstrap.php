<?php

namespace Blog;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class Bootstrap
 *
 * @package Blog
 */
class Bootstrap extends AbstractBootstrap {
    
    public function __construct() {
        $this->dependencies = [
        ];

        $this->endpoints = [
        ];
        
        $this->middlewares = [
        ];
        
        $this->models = [
        ];
        
        $this->services = [
        ];
    }

}
