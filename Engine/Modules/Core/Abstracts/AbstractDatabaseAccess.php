<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Class AbstractModel
 * (Database) Models from Modules or Plugins inherits from AbstractModel.
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractDatabaseAccess {

    private $entityManger;
    private $repository;
    private $models;

    public function __construct($models) {
        $this->models = $models;
    }

    public function entityManager() : EntityManager {
        if (!isset($this->entityManger)) {
            $this->entityManger = Oforge()->DB()->getManager();
        }

        return $this->entityManger;
    }

    public function repository($name = "default") : EntityRepository {
        if (!isset($this->repository[$name])) {
            $this->repository[$name] = $this->entityManager()->getRepository($this->models[$name]);
        }

        return $this->repository[$name];
    }

    /**
     * Returns repository for given class.
     *
     * @param string $class
     *
     * @return EntityRepository
     */
    protected function getRepository(string $class) : EntityRepository {
        return $this->entityManager()->getRepository($class);
    }

}
