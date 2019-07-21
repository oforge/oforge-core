<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Forge\ForgeEntityManager;

/**
 * Class AbstractModel
 * (Database) Models from Modules or Plugins inherits from AbstractModel.
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractDatabaseAccess {
    /** @var ForgeEntityManager $forgeEntityManger */
    private $forgeEntityManger;
    /** @var array $repositories */
    private $repositories;
    /** @var array $models */
    private $models;

    /**
     * AbstractDatabaseAccess constructor.
     *
     * @param string|array $models
     */
    public function __construct($models) {
        $this->models = is_string($models) ? ['default' => $models] : $models;
    }

    /** @return ForgeEntityManager */
    public function entityManager() : ForgeEntityManager {
        if (!isset($this->forgeEntityManger)) {
            $this->forgeEntityManger = Oforge()->DB()->getForgeEntityManager();
        }

        return $this->forgeEntityManger;
    }

    /**
     * @param string $name
     *
     * @return EntityRepository
     * @throws ORMException
     */
    public function repository($name = 'default') : EntityRepository {
        if (!isset($this->repositories[$name])) {
            $this->repositories[$name] = $this->entityManager()->getRepository($this->models[$name]);
        }

        return $this->repositories[$name];
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
