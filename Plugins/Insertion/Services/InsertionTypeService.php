<?php

namespace Insertion\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionType;
use Insertion\Models\InsertionTypeAttribute;
use Insertion\Models\InsertionTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class InsertionTypeService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default'                => InsertionType::class,
            'insertionTypeAttribute' => InsertionTypeAttribute::class,
            'group'                  => InsertionTypeGroup::class,
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

        $this->entityManager()->create($insertionType);

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
     * @return array
     * @throws ORMException
     */
    public function getInsertionTypeTree($parent = null) {
        $all  = $this->repository()->findAll();
        $tree = [];

        $tree = $this->buildTree($parent, $all);

        return $tree;
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
     * @param string $attributeGroup
     * @param bool $required
     *
     * @throws ORMException
     */
    public function addAttributeToInsertionType($insertionType, $attributeKey, $isTop, $attributeGroup = 'main', $required = false) {
        $group = $this->repository("group")->findOneBy(["name" => $attributeGroup]);
        if (!isset($group)) {
            $group = InsertionTypeGroup::create(["name" => $attributeGroup]);
            $this->entityManager()->create($group);
        }

        $insertionTypeAttribute = new InsertionTypeAttribute();
        $insertionTypeAttribute->setInsertionType($insertionType)->setAttributeKey($attributeKey)->setIsTop($isTop)->setAttributeGroup($group)
                               ->setRequired($required);

        $this->entityManager()->create($insertionTypeAttribute);

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
    }

}
