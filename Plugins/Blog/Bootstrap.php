<?php

namespace Blog;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Test\Middleware\HomeMiddleware;
use Test\Models\Test\Test;
use Test\Services\TestService;

/**
 * Class Bootstrap
 *
 * @package Blog
 */
class Bootstrap extends AbstractBootstrap {
    
    public function __construct() {

        $this->dependencies = [
            \Test2\Bootstrap::class
        ];
        $this->endpoints = [
            // '/' => ['controller' => \Test\Controller\Frontend\HomeController::class, 'name' => 'Blog']
        ];
        
        $this->middlewares = [
            'home2' => ['class' => HomeMiddleware::class, 'position' => 0]
        ];
        
        $this->models = [
            Test::class
        ];
        
        $this->services = [
            'test.test' => TestService::class
        ];
    }
    
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /** @var TestService $testService */
        $testService = Oforge()->Services()->get('test.test');
        $testService->addTestData();
    }

}
