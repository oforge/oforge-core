<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class InsertionTypeService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                => InsertionType::class,
            'insertionTypeAttribute' => InsertionTypeAttribute::class,
        ]);
    }

    /**
     * @param $name
     * @param null $parent
     *
     * @return InsertionType
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

        return $insertionType;
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

    /**
     * @param InsertionType $insertionType
     * @param AttributeKey $attributeKey
     * @param $isTop
     * @param string $attributeGroup
     * @param bool $required
     *
     * @throws ORMException
     */
    public function addAttributeToInsertionType($insertionType, $attributeKey, $isTop, $attributeGroup = 'main', $required = false) {
        $insertionTypeAttribute = new InsertionTypeAttribute();
        $insertionTypeAttribute->setInsertionType($insertionType)->setAttributeKey($attributeKey)->setIsTop($isTop)->setAttributeGroup($attributeGroup)
                               ->setRequired($required);
        $this->entityManager()->persist($insertionTypeAttribute);
        $this->entityManager()->flush($insertionTypeAttribute);
        $insertionType->setAttributes([$attributeKey]);
        // $insertionType->addAttribute()
    }

    /**
     * @param $insertionTypeId
     * @param $attributeId
     *
     * @throws ORMException
     */
    public function removeAttributeFromInsertionType($insertionTypeId, $attributeId) {
        $candidate = $this->repository('insertionTypeAttribute')->findOneBy([
            'insertionId' => $insertionTypeId,
            'attributeId' => $attributeId,
        ]);
        $this->entityManager()->remove($candidate);
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
