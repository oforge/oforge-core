<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class AttributeService extends GenericCrudService {

    public function __construct() {
        parent::__construct([
            'attributeKey' => AttributeKey::class,
            'attributeValue' => AttributeValue::class,
        ]);
    }

    /**
     * @param $name
     * @param $type
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeKey($name, $type) {
        $attributeKey = new AttributeKey();
        $attributeKey->setName($name);
        $attributeKey->setType($type);

        $this->entityManager()->persist($attributeKey);
        $this->entityManager()->flush();
    }

    /**
     * @param $value
     * @param null $subAttributeKey
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeValue($value, $subAttributeKey = null) {
        $attributeValue = new AttributeValue();
        $attributeValue->setValue($value);
        $attributeValue->setSubAttributeKeyId($subAttributeKey);

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
        $attributeValue->setSubAttributeKeyId($values['subAttributeKey']);

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
