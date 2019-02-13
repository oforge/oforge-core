<?php

namespace FrontendUserManagement\Services;

use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;

class FrontendSecureMiddlewareService {
    private $entityManager;
    private $repository;
    
    public function __construct() {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(Middleware::class);
    }
    
    /**
     * @param string $middlewareName
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function activate(string $middlewareName) {
        $this->changeActiveState($middlewareName, true);
    }
    
    /**
     * @param string $middlewareName
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deactivate(string $middlewareName) {
        $this->changeActiveState($middlewareName, false);
    }
    
    /**
     * @param $middlewareName
     * @param $state
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function changeActiveState($middlewareName, $state) {
        $middleware = $this->repository->findOneBy(['name' => $middlewareName]);
        $middleware->setActive($state);
        $this->entityManager->persist($middleware);
        $this->entityManager->flush($middleware);
    }
}