<?php

namespace Insertion\Services;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Annotation\Cache\Cache;

/**
 * Class InsertionTypeService
 *
 * @package Insertion\Services
 */
class InsertionTypeService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                => InsertionType::class,
            'insertionTypeAttribute' => InsertionTypeAttribute::class,
            'group'                  => InsertionTypeGroup::class,
            'attributeKey'           => AttributeKey::class,
        ]);
    }

    /**
     * @param $name
     * @param null $parent
     * @param bool $quickSearch
     *
     * @return InsertionType
     * @throws ORMException
     */
    public function createNewInsertionType($name, $parent = null, $quickSearch = false) {
        /** @var InsertionType $insertionType */
        $insertionType = new InsertionType();
        $insertionType->setName($name);
        $insertionType->setParent($parent);
        $insertionType->setQuickSearch($quickSearch);

        $this->entityManager()->create($insertionType);

        return $insertionType;
    }

    /**
     * @param $name
     *
     * @return object
     * @throws ORMException
     */
    public function getInsertionTypeByName($name) {
        return $this->repository()->findOneBy(['name' => $name]);
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
    public function getInsertionTypeList($limit, $offset) {
        return $this->repository()->findBy([], null, $limit, $offset);
    }

    /**
     *
     * @return array
     * @throws ORMException
     */
    public function getInsertionTypeTree($parent = null) {
        $all  = $this->repository()->findAll();
        $tree = [];

        $tree = $this->buildTree($parent, $all);

        return $tree;
    }

    public function getInsertionTypeAttributeMap() {
        $attributes = $this->repository("attributeKey")->findAll();

        $result = [];
        foreach ($attributes as $attribute) {
            $result[$attribute->getId()] = $attribute->toArray();
        }

        return $result;
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getInsertionTypeAttributeTree($typeId) {
        $all    = $this->repository("insertionTypeAttribute")->findBy(["insertionType" => $typeId]);
        $groups = $this->repository("group")->findBy([], ["order" => "asc"]);

        $result = [];
        $map    = [];
        foreach ($groups as $group) {
            $map[$group->getName()] = sizeof($result);
            $result[]               = ["name" => $group->getName(), "items" => []];
        }

        $map["default"] = sizeof($result);
        $result[]       = ["name" => "default", "items" => []];

        /**
         * @var $attr InsertionTypeAttribute
         */
        foreach ($all as $attr) {
            $group     = $attr->getAttributeGroup();
            $groupName = "default";
            if (isset($group)) {
                $groupName = $group->getName();
            }

            if (isset($map[$groupName]) && isset($result[$map[$groupName]]) && isset($result[$map[$groupName]]["items"])) {
                $result[$map[$groupName]]["items"][] = $attr->toArray(3);
            }
        }

        return $result;
    }

    private function buildTree($parent, array $all) {
        /**
         * @var $item InsertionType
         */
        $result = [];

        foreach ($all as $item) {
            if (($item->getParent() == null && $parent == null) || ($item->getParent() != null && $item->getParent()->getId() == $parent)) {
                $data     = $item->toArray(1);
                $children = $this->buildTree($item->getId(), $all);

                if (sizeof($children) > 0) {
                    $data["children"] = $children;
                }
                array_push($result, $data);
            }
        }

        return $result;
    }

    /**
     * @param InsertionType $insertionType
     * @param AttributeKey $attributeKey
     * @param $isTop
     * @param int $insertionTypeGroup
     * @param bool $required
     * @param bool $isQuickSearchFilter
     * @param $quickSearchOrder
     *
     * @throws ORMException
     */
    public function addAttributeToInsertionType($insertionType, $attributeKey, $isTop, $insertionTypeGroup, $required = false, $isQuickSearchFilter = false, $quickSearchOrder) {
        /** @var InsertionTypeGroup $group */
        $group = $this->repository("group")->find($insertionTypeGroup);

        $insertionTypeAttribute = new InsertionTypeAttribute();
        $insertionTypeAttribute
            ->setInsertionType($insertionType)
            ->setAttributeKey($attributeKey)
            ->setIsTop($isTop)
            ->setAttributeGroup($group)
            ->setRequired($required)
            ->setIsQuickSearchFilter($isQuickSearchFilter)
            ->setQuickSearchOrder($quickSearchOrder);

        $insertionType->setAttributes([$attributeKey]);
        $this->entityManager()->update($insertionTypeAttribute);

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
        /** @var InsertionType $insertionType */
        $insertionType = $this->repository()->find($id);
        /** @var InsertionTypeAttribute $attribute */
        foreach ($insertionType->getAttributes() as $attribute) {
            $this->deleteInsertionTypeAttribute($attribute->getId());
        }
        $this->entityManager()->remove($insertionType);
    }

    /**
     * @return mixed
     * @throws ORMException
     * @throws NonUniqueResultException
     */
    public function getInsertionTypeCount() {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $result       = $queryBuilder->select($queryBuilder->expr()->count('t.id'))->from(InsertionType::class, 't');

        return $result->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getAttributeGroupList() {
        return $this->repository('group')->findAll();
    }

    /**
     * @param $id
     * @param $name
     * @param $parent
     * @param $quickSearch
     *
     * @return object|null
     * @throws ORMException
     */
    public function updateInsertionType($id, $data) {
        /** @var InsertionType $insertionType */
        $insertionType = $this->repository()->find($id);

        $insertionType->fromArray($data);

        $this->entityManager()->update($insertionType);

        return $insertionType;
    }

    /**
     * @param $id
     * @param $attributeKey
     * @param $top
     * @param $insertionTypeGroup
     * @param $required
     * @param $isQuickSearchFilter
     * @param $quickSearchOrder
     *
     * @return InsertionTypeAttribute
     * @throws ORMException
     */
    public function updateInsertionTypeAttribute($id, $attributeKey, $top, $insertionTypeGroup, $required, $isQuickSearchFilter, $quickSearchOrder) {
        /** @var InsertionTypeAttribute $insertionTypeAttribute */
        $insertionTypeAttribute = $this->repository('insertionTypeAttribute')->find($id);
        /** @var InsertionTypeGroup $group */
        $group = $this->repository('group')->findOneBy(['id' => $insertionTypeGroup]);

        $insertionTypeAttribute->setAttributeKey($attributeKey);
        $insertionTypeAttribute->setIsTop($top);
        $insertionTypeAttribute->setAttributeGroup($group);
        $insertionTypeAttribute->setRequired($required);
        $insertionTypeAttribute->setIsQuickSearchFilter($isQuickSearchFilter);
        $insertionTypeAttribute->setQuickSearchOrder($quickSearchOrder);

        $this->entityManager()->update($insertionTypeAttribute);

        return $insertionTypeAttribute;
    }

    /**
     * @param $id
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function deleteInsertionTypeAttribute($id) {
        $insertionTypeAttribute = $this->repository('insertionTypeAttribute')->find($id);
        $this->entityManager()->remove($insertionTypeAttribute);
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getQuickSearchInsertions() {
        return $this->repository()->findBy(['quickSearch' => true]);
    }
}
