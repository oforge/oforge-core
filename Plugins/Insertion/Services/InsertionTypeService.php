<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\InsertionType;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class InsertionTypeService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(['default' => InsertionType::class]);
    }

    /**
     * @param $name
     * @param null $parent
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewInsertionType($name, $parent = null) {
        /** @var InsertionType $insertionType */
        $insertionType = new InsertionType();
        $insertionType->setName($name);
        $insertionType->setParent($parent);

        $this->entityManager()->persist($insertionType);
        $this->entityManager()->flush();
    }

    /**
     * @param $name
     *
     * @return array|null
     * @throws ORMException
     */
    public function getInsertionTypeByName($name) {
        return $this->repository()->findBy(['name' => $name]);
    }

    /**
     * @param $id
     *
     * @return object|null
     * @throws ORMException
     */
    public function getInsertionTypeById($id) {
        return $this->repository()->find($id);
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getInsertionTypeList() {
        return $this->repository()->findAll();
    }

    public function addAttributeToInsertionType($attributes) {
        // TODO
    }

    public function removeAttributeFromInsertionType($attributeId) {
        // TODO
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteInsertionType($id) {
        $insertionType = $this->repository()->find($id);
        $this->entityManager()->remove($insertionType);
        $this->entityManager()->flush();
    }
}
