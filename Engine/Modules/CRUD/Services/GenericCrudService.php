<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 06.12.2018
 * Time: 11:11
 */

namespace Oforge\Engine\Modules\CRUD\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;

/**
 * Class GenericCrudService
 *
 * @package Oforge\Engine\Modules\CRUD\Services;
 */
class GenericCrudService {
    /** @var EntityManager $entityManager */
    private $entityManager;

    /**
     * GenericCrudService constructor.
     */
    public function __construct() {
        $this->entityManager = Oforge()->DB()->getManager();
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
        /** @var AbstractModel[] $items */
        if (empty($params)) {
            //TODO
            $items = $repository->findAll();
        } else {
            $items = $repository->findAll();
        }
        $result = [];
        foreach ($items as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    /**
     * TODO @MS
     *
     * @param string $class
     *
     * @return array
     */
    public function definition(string $class) : array {
        if (is_subclass_of($class, AbstractModel::class)) {
            return $class::definition();
        }

        return [];
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
        $result = $repo->findOneBy(["id" => $id]);

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
     */
    public function create(string $class, array $options) {
        $repository = $this->getRepository($class);

        if (isset($options['id'])) {
            $id     = $options['id'];
            $entity = $repository->findOneBy(['id' => $id]);
            if (isset($entity)) {
                throw new ConfigElementAlreadyExists("Entity with id '$id' already exists!");
            }
        }
        /** @var AbstractModel $instance */
        $instance = new $class();
        $instance = $instance->fromArray($options);

        $this->entityManager->persist($instance);
        $this->entityManager->flush($instance);
        $repository->clear();
    }

    /**
     * Update entity.
     *
     * @param string $class
     * @param array $options
     *
     * @throws NotFoundException
     * @throws ConfigOptionKeyNotExists
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(string $class, array $options) {
        if (!isset($options['id'])) {
            throw new ConfigOptionKeyNotExists('id');
        }
        $repository = $this->getRepository($class);
        $id         = $options['id'];
        // $objects = $this->structure($options);
        // foreach ($objects as $id => $el) {
        $entity = $repository->findOneBy(["id" => $id]);

        if (!isset($entity)) {
            throw new NotFoundException("Entity with id $id not found!");
        }

        $entity->fromArray($options);
        // }
        $this->entityManager->flush();
        $repository->clear();
    }

    /**
     * Delete entity by id.
     *
     * @param string $class
     * @param int $id
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(string $class, int $id) {
        $repository = $this->getRepository($class);
        $entity     = $repository->findOneBy(['id' => $id]);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
        $repository->clear();
    }

    /**
     * Returns repository for given class.
     *
     * @param string $class
     *
     * @return EntityRepository
     */
    protected function getRepository(string $class) : EntityRepository {
        return $this->entityManager->getRepository($class);
    }

    /**
     * TODO @MS ?????????????
     *
     * @param $options
     *
     * @return array
     */
    protected function structure($options) {
        $result = [];
        foreach ($options as $key => $value) {
            $ex = explode("_", $key);
            if (sizeof($ex) == 2) {
                if (!isset($result[$ex[0]])) {
                    $result[$ex[0]] = [];
                }

                $result[$ex[0]][$ex[1]] = $value;
            }
        }

        return $result;
    }

}
