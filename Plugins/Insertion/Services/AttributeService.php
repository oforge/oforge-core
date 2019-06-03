<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class AttributeService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'attributeKey'   => AttributeKey::class,
            'attributeValue' => AttributeValue::class,
        ]);
    }

    /**
     * @param $name
     * @param $inputType
     * @param $filterType
     *
     * @return AttributeKey
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeKey($name, $inputType, $filterType) {
        $attributeKey = new AttributeKey();
        $attributeKey->setName($name);
        $attributeKey->setType($inputType);
        $attributeKey->setFilterType($filterType);

        $this->entityManager()->persist($attributeKey);
        $this->entityManager()->flush();

        return $attributeKey;
    }

    /**
     * @param $value
     * @param AttributeKey $attributeKey
     * @param null $subAttributeKey
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeValue($value, $attributeKey, $subAttributeKey = null) {
        $attributeValue = new AttributeValue();
        $attributeValue->setAttributeKey($attributeKey);
        $attributeValue->setValue($value);
        $attributeValue->setSubAttributeKey($subAttributeKey);

        $this->entityManager()->persist($attributeValue);
        $this->entityManager()->flush();
    }

    /**
     * @param $values
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeValues($values) {
        foreach ($values as $value) {
            $this->createNewAttributeValue($value['value'], $value['$subAttributeKey']);
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $type
     * @param $filterType
     *
     * @return AttributeKey
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateAttributeKey($id, $name, $type, $filterType) {
        /** @var AttributeKey $attributeKey */
        $attributeKey = $this->repository('attributeKey')->find($id);
        $attributeKey->setName($name);
        $attributeKey->setType($type);
        $attributeKey->setFilterType($filterType);

        $this->entityManager()->persist($attributeKey);
        $this->entityManager()->flush();

        return $attributeKey;
    }

    /**
     * @param $id
     * @param $value
     * @param null $subAttributeKey
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateAttributeValue($id, $value, $subAttributeKey = null) {
        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->repository('attributeValue')->find($id);
        $attributeValue->setValue($value);
        $attributeValue->setSubAttributeKey($subAttributeKey);

        $this->entityManager()->persist($attributeValue);
        $this->entityManager()->flush();
    }

    /**
     * @param $id
     *
     * @return AttributeKey|null
     * @throws ORMException
     */
    public function getAttribute($id) {
        /** @var AttributeKey $attributeKey */
        $attributeKey = $this->repository('attributeKey')->find($id);

        return $attributeKey;
    }

    /**
     * @param null $offset
     * @param null $limit
     *
     * @return array
     * @throws ORMException
     */
    public function getAttributeList($limit = null, $offset = null) {
        return $this->repository('attributeKey')->findBy([], null, $limit, $offset);
    }

    /**
     * @throws ORMException
     */
    public function getAttributeCount() {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result       = $queryBuilder->select($queryBuilder->expr()->count('a.id'))->from(AttributeKey::class, 'a');

        return $result->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAttributeKey($id) {
        $attributeKey = $this->repository('attributeKey')->find($id);
        $this->entityManager()->remove($attributeKey);
        $this->entityManager()->flush();
        $this->repository('attributeKey')->clear();
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAttributeValue($id) {
        $attributeValue = $this->repository('attributeValue')->find($id);
        $this->entityManager()->remove($attributeValue);
        $this->entityManager()->flush();
    }
}
