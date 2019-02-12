<?php

namespace Oforge\Engine\Modules\Core\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Models\Plugin\Middleware;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;

class MiddlewareService
{
    /**
     * @param $name
     *
     * @return array|Middleware[]
     */
    public function getActive($name)
    {
        $entityManager = Oforge()->DB()->getManager();
        $repository = $entityManager->getRepository(Middleware::class);

        $middlewares = $repository->findBy(["name" => [$name], "active" => 1], ['position' => 'DESC']);
        /*
        $queryBuilder = $entityManager->createQueryBuilder();
        $result = $queryBuilder->select(array('m'))
            ->from(Middleware::class, 'm')
            ->where($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('m.name', '?1'),
                $queryBuilder->expr()->eq('m.name', '?2'),
                $queryBuilder->expr()->like('m.name', '?3')
            ))
            ->andWhere($queryBuilder->expr()->eq('m.active', 1))
            ->orderBy('m.position', 'DESC')->setParameters([1 => $name, 2 => '*', 3 => $name . '_%'])->getQuery();
        $middlewares = $result->execute();
        */

        return $middlewares;
    }
    
    /**
     * get all active middlewares
     * @return array|null
     */
    public function getAllActiveNames() {
        $entityManager = Oforge()->DB()->getManager();
        $repository = $entityManager->getRepository(Middleware::class);
        $middlewares = $repository->findBy(["active" => 1], ['position' => 'DESC']);
        
        $names = [];
        foreach ($middlewares as $middleware) {
            array_push($names, $middleware->getName());
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
                    $repository = Oforge()->DB()->getManager()->getRepository(Middleware::class);

                    $element = $repository->findOneBy(["class" => $option["class"]]);
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
            $repository = Oforge()->DB()->getManager()->getRepository(Middleware::class);

            foreach ($options as $key => $option) {
                if ($this->isValid($option)) {

                    $element = $repository->findOneBy(["class" => $option["class"]]);
                    if(!isset($element)) {
                        $element = Middleware::create(["name" => $key,  "class" => $option["class"], "active" => 1, "position" => $option["position"]]);
                        Oforge()->DB()->getManager()->persist($element);
                    }
                }
            }
        }

        Oforge()->DB()->getManager()->flush();
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
}
