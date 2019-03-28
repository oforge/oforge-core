<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\CRUD\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;

/**
 * Class GenericCrudService
 *
 * @package Oforge\Engine\Modules\CRUD\Services;
 */
class GenericCrudService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([]);
    }

    /**
     * TODO @MS
     *
     * @param string $class
     * @param array $params
     *
     * @return array
     */
    public function list(string $class, array $params = []) : array {
        $repository = $this->getRepository($class);
        /** @var AbstractModel[] $entities */
        if (empty($params)) {
            $entities = $repository->findAll();
        } else {
            $entities = $repository->findBy($params);
        }
        $result = [];
        foreach ($entities as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    /**
     * TODO @MS
     *
     * @param string $class
     * @param int $id
     *
     * @return object|null
     */
    public function getById(string $class, int $id) {
        $repo   = $this->getRepository($class);
        $result = $repo->findOneBy([
            'id' => $id,
        ]);

        return $result;
    }

    /**
     * Create entity if not exist yet.
     *
     * @param string $class
     * @param array $options
     *
     * @throws ConfigElementAlreadyExists
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \ReflectionException
     */
    public function create(string $class, array $options) {
        $repository = $this->getRepository($class);

        if (isset($options['id'])) {
            $id     = $options['id'];
            $entity = $repository->findOneBy([
                'id' => $id,
            ]);
            if (isset($entity)) {
                throw new ConfigElementAlreadyExists("Entity with id '$id' already exists!");
            }
        }
        /** @var AbstractModel $instance */
        $instance = new $class();
        $instance = $instance->fromArray($options);

        $this->entityManager()->persist($instance);
        $this->entityManager()->flush($instance);
        $repository->clear();
    }

    /**
     * Update entity.
     *
     * @param string $class
     * @param array $options
     *
     * @throws NotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(string $class, array $options) {
        $repository = $this->getRepository($class);

        if (isset($options['id'])) {
            $id = $options['id'];
            unset($options['id']);
            $entity = $repository->findOneBy([
                'id' => $id,
            ]);
            if (!isset($entity)) {
                throw new NotFoundException("Entity with id '$id' not found!");
            }
            $entity->fromArray($options);
        } elseif (isset($options['data'])) {
            $objectsData = $options['data'];
            foreach ($objectsData as $id => $objectData) {
                // echo "<pre>", print_r($id, true), "</pre>";
                // echo "<pre>", print_r($objectData, true), "</pre>";
                $entity = $repository->findOneBy([
                    'id' => $id,
                ]);
                if (!isset($entity)) {
                    throw new NotFoundException("Entity with id '$id' not found!");
                }
                $entity->fromArray($objectData);
            }
        }
        $this->entityManager()->flush();
        $repository->clear();
    }

    /**
     * Delete entity by id.
     *
     * @param string $class
     * @param int $id
     *
     * @throws NotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(string $class, int $id) {
        $repository = $this->getRepository($class);

        $entity = $repository->findOneBy([
            'id' => $id,
        ]);
        if (!isset($entity)) {
            throw new NotFoundException("Entity with id '$id' not found!");
        }
        $this->entityManager()->remove($entity);
        $this->entityManager()->flush();
        $repository->clear();
    }

}
