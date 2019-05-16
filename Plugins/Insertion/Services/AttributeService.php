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
            'attributeKey' => AttributeKey::class,
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
     * @param array $values
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateAttributeValue($id, $values) {
        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->repository('attributeValue')->find($id);
        $attributeValue->setValue($values['value']);
        $attributeValue->setSubAttributeKey($values['subAttributeKey']);

        $this->entityManager()->persist($attributeValue);
        $this->entityManager()->flush();
    }

    /**
     * @param $id
     *
     * @return object|null
     * @throws ORMException
     */
    public function getAttribute($id) {
        return $this->repository('attributeKey')->find($id);
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getAttributeList() {
        return $this->repository('attributeKey')->findAll();
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
