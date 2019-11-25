<?php

namespace Insertion\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\AttributeValue;
use Insertion\Models\InsertionAttributeValue;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class AttributeService extends AbstractDatabaseAccess
{

    public function __construct()
    {
        parent::__construct([
            'attributeKey' => AttributeKey::class,
            'attributeValue' => AttributeValue::class,
            'insertionAttributeValue' => InsertionAttributeValue::class
        ]);
    }

    /**
     * @param $name
     * @param $inputType
     * @param $filterType
     * @param $inputTypeRestrictions
     *
     * @return AttributeKey
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeKey($name, $inputType, $filterType, $inputTypeRestrictions = null)
    {
        $attributeKey = new AttributeKey();
        $attributeKey->setName($name);
        $attributeKey->setType($inputType);
        $attributeKey->setFilterType($filterType);
        $attributeKey->setRestrictions($inputTypeRestrictions);

        $this->entityManager()->create($attributeKey);

        return $attributeKey;
    }

    /**
     * @param $value
     * @param AttributeKey $attributeKey
     * @param null $subAttributeKey
     * @param int $hierarchyOrder
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeValue($value, $attributeKey, $hierarchyOrder = 0, $subAttributeKey = null)
    {
        $attributeValue = new AttributeValue();
        $attributeValue->setAttributeKey($attributeKey);
        $attributeValue->setValue($value);
        $attributeValue->setHierarchyOrder($hierarchyOrder);
        $attributeValue->setSubAttributeKey($subAttributeKey);

        $this->entityManager()->create($attributeValue);
    }

    /**
     * @param $values
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewAttributeValues($values)
    {
        foreach ($values as $value) {
            $this->createNewAttributeValue($value['value'], $value['$subAttributeKey']);
        }
    }

    /**
     * @param $id
     * @param $name
     * @param $type
     * @param $filterType
     * @param bool $sortable
     * @param $inputTypeRestrictions
     * @param bool $hierarchical
     *
     * @return AttributeKey
     * @throws ORMException
     */
    public function updateAttributeKey($id, $name, $type, $filterType, $inputTypeRestrictions = "", $sortable = false, $hierarchical = false)
    {
        /** @var AttributeKey $attributeKey */
        $attributeKey = $this->repository('attributeKey')->find($id);
        $attributeKey->setName($name);
        $attributeKey->setType($type);
        $attributeKey->setFilterType($filterType);
        $attributeKey->setRestrictions($inputTypeRestrictions != null ? $inputTypeRestrictions : "");
        $attributeKey->setSortable($sortable);
        $attributeKey->setHierarchical($hierarchical);
        $this->entityManager()->update($attributeKey);

        return $attributeKey;
    }

    /**
     * @param $id
     * @param $value
     * @param int $hierarchyOrder
     * @param null $subAttributeKey
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateAttributeValue($id, $value, $hierarchyOrder = 0, $subAttributeKey = null)
    {
        /** @var AttributeValue $attributeValue */
        $attributeValue = $this->repository('attributeValue')->find($id);
        $attributeValue->setValue($value);
        $attributeValue->setHierarchyOrder($hierarchyOrder);
        $attributeValue->setSubAttributeKey($subAttributeKey);

        $this->entityManager()->update($attributeValue);
    }

    /**
     * @param $id
     *
     * @return AttributeKey|null
     * @throws ORMException
     */
    public function getAttribute($id)
    {
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
    public function getAttributeList($limit = null, $offset = null)
    {
        return $this->repository('attributeKey')->findBy([], null, $limit, $offset);
    }

    /**
     * @throws ORMException
     */
    public function getAttributeCount()
    {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result = $queryBuilder->select($queryBuilder->expr()->count('a.id'))->from(AttributeKey::class, 'a');

        return $result->getQuery()->getSingleScalarResult($id);
    }

    /**
     * @param AttributeKey $attributeKey
     * @param AttributeValue $attributeValues
     * @throws ORMException
     * @return array $attributeValues
     */
    public function getAllValuesBetterThan($attributeKey, $attributeValues){
        /** @var AttributeValue[] $allValues */
        $allValues = $attributeKey->getValues();
        $higherValues = [];

        $lowestValue = 0;
        foreach ($attributeValues as $attributeValue) {
            /** @var AttributeValue $attributeValue */
            $attributeValue = $this->repository('attributeValue')->findOneBy(['id' => $attributeValue]);

            if($attributeValue->getHierarchyOrder() > $lowestValue) {
                $lowestValue = $attributeValue->getHierarchyOrder();
            }
        }
        foreach ($allValues as $singleValue) {
            if($singleValue->getHierarchyOrder() > $lowestValue) {
                $higherValues[] = $singleValue->getId();
            }
        }

        return $higherValues;
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAttributeKey($id)
    {
        $attributeKey = $this->repository('attributeKey')->find($id);
        $this->entityManager()->remove($attributeKey);
        $this->repository('attributeKey')->clear();
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteAttributeValue($id)
    {
        $attributeValue = $this->repository('attributeValue')->find($id);
        $this->entityManager()->remove($attributeValue);
    }

    /**
     * @param array $attributeKeys
     * @return array $attributeValues
     * @throws ORMException
     */
    public function getAllAttributeValues($attributeKeys)
    {
        $attributeValues = [];
        //Find all attribute containing the given attribute keys and push their value
        foreach ($attributeKeys as $attributeKey) {
            /** @var InsertionAttributeValue $attribute */
            foreach ($this->repository('insertionAttributeValue')->findBy(['attributeKey' => $attributeKey]) as $attribute) {
                /** @var string $attributeValue */
                $attributeValue = $attribute->getValue();
                if ($attributeValue !== null ? $attributeValue != "" : false) {
                    array_push($attributeValues, $attributeValue);
                }
            }
        }
        return array_unique($attributeValues);
    }
}
