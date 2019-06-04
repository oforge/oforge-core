<?php

namespace Oforge\Engine\Modules\Core\Forge;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;

/**
 * Class ForgeEntityManager
 *
 * @package Oforge\Engine\Modules\Core\Forge
 */
class ForgeEntityManager {
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * ForgeEntityManager constructor.
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string|null $className
     *
     * @throws MappingException
     */
    public function clear(?string $className = null) {
        $this->entityManager->clear($className);
    }

    /**
     * Calls persist and (optional) flush on EntityManager.
     *
     * @param object $entity
     * @param bool $flush
     *
     * @return ForgeEntityManager
     * @throws ORMException
     */
    public function create($entity, $flush = true) : ForgeEntityManager {
        $this->entityManager->persist($entity);
        if ($flush) {
            $this->entityManager->flush($entity);
        }

        return $this;
    }

    /**
     * If not managed, merge flush is called on EntityManager and then (optional) flush.
     *
     * @param object $entity
     * @param bool $flush
     *
     * @return ForgeEntityManager
     * @throws ORMException
     */
    public function update(&$entity, $flush = true) : ForgeEntityManager {
        if (!$this->entityManager->contains($entity)) {
            $entity = $this->entityManager->merge($entity);
        }
        if ($flush) {
            $this->entityManager->flush($entity);
        }

        return $this;
    }

    /**
     * @param object $entity
     * @param bool $flush
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove($entity, $flush = true) {
        $this->entityManager->remove($entity);
        if ($flush) {
            $this->entityManager->flush($entity);
        }
    }

    /**
     * @param object|null $entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush($entity = null) {
        $this->entityManager->flush($entity);
    }

    /**
     * @param string $entityName
     *
     * @return EntityRepository
     */
    public function getRepository(string $entityName) : EntityRepository {
        return $this->entityManager->getRepository($entityName);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager() : EntityManager {
        return $this->entityManager;
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder() : QueryBuilder {
        return $this->entityManager->createQueryBuilder();
    }

}
