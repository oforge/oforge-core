<?php

namespace Oforge\Engine\Modules\Core\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;

class MiddlewareService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => Middleware::class]);
    }

    /**
     * @param $name
     *
     * @return array|Middleware[]
     */
    public function getActive($name)
    {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result = $queryBuilder->select(array('m'))
            ->from(Middleware::class, 'm')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('m.name', '?1')
            ))
            ->andWhere($queryBuilder->expr()->eq('m.active', 1))
            ->orderBy('m.position', 'DESC')
            ->setParameters([1 => $name])
            ->getQuery();
        $middlewares = $result->execute();

        return $middlewares;
    }

    /**
     * get all active middlewares
     *
     * @return array|null
     */
    public function getAllDistinctActiveNames() {

        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result = $queryBuilder->select(array('m.name'))
           ->from(Middleware::class, 'm')
           ->where($queryBuilder->expr()->eq('m.active', 1))
           ->andWhere($queryBuilder->expr()->neq('m.name', '?1'))
           ->groupBy("m.name")
           ->setParameters([1 => '*'])
           ->getQuery();
        $middlewares = $result->execute();
        
        $names = [];
        
        foreach ($middlewares as $middleware) {
            array_push($names, $middleware['name']);
        }
        
        return $names;
    }

    /**
     * @param $options
     * @param $middleware
     *
     * @return Middleware[]
     */
    public function register($options, $middleware)
    {
        /**
         * @var $result Middleware[]
         */
        $result = [];
        if (is_array($options)) {

            foreach ($options as $key => $option) {
                if ($this->isValid($option)) {
                    /**
                     * Check if the element is already within the system
                     */
                    $element = $this->repository()->findOneBy(["class" => $option["class"]]);
                    if(!isset($element)) {
                        $element = Middleware::create(["name" => $key,  "class" => $option["class"], "position" => $option["position"]]);
                        $element->setPlugin($middleware);
                    }

                    array_push($result, $element);
                }
            }
        }

        return $result;
    }
    
    /**
     * @param $options
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registerFromModule($options)
    {
        if (is_array($options)) {
            /**
             * Check if the element is already within the system
             */
            foreach ($options as $key => $option) {
                if ($this->isValid($option)) {

                    $element = $this->repository()->findOneBy(["class" => $option["class"]]);
                    if(!isset($element)) {
                        $element = Middleware::create(["name" => $key,  "class" => $option["class"], "active" => 1, "position" => $option["position"]]);
                        $this->entityManager()->persist($element);
                    }
                }
            }
        }

        $this->entityManager()->flush();
    }
    
    /**
     * @param array $options
     *
     * @return bool
     */
    private function isValid(Array $options)
    {
        /**
         * Check if required keys are within the options
         */
        $keys = ["class"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) throw new ConfigOptionKeyNotExists($key);
        }

        /*
         * Check if correct type are set
         */
        if (isset($options["position"]) && !is_integer($options["position"])) {
            throw new \InvalidArgumentException("Position value should be of type integer. ");
        }
        return true;
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
        $middleware = $this->repository()->findOneBy(['name' => $middlewareName]);
        $middleware->setActive($state);
        $this->entityManager()->persist($middleware);
        $this->entityManager()->flush($middleware);
    }
}
