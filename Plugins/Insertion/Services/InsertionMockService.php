<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Enums\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\InsertionType;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\CRUD\Services\GenericCrudService;

class InsertionMockService {

    public static function init() {

    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public static function createAttributes() {
        $attributes = [];
        /** @var AttributeService $attributeService */
        $attributeService = Oforge()->Services()->get('insertion.attribute');

        $attributeKey = $attributeService->createNewAttributeKey('GameType', AttributeType::SINGLE, AttributeType::MULTI);
        $attributeService->createNewAttributeValue('Strategy', $attributeKey);
        $attributeService->createNewAttributeValue('FPS', $attributeKey);
        $attributeService->createNewAttributeValue('Racing', $attributeKey);

        array_push($attributes, $attributeKey);

        $subAttributeKey = $attributeService->createNewAttributeKey('Rank', AttributeType::SINGLE, AttributeType::MULTI);
        $attributeService->createNewAttributeValue('Gold', $attributeKey);
        $attributeService->createNewAttributeValue('Silver', $attributeKey);
        $attributeService->createNewAttributeValue('Bronce', $attributeKey);

        $attributeKey = $attributeService->createNewAttributeKey('Discipline', AttributeType::MULTI, AttributeType::MULTI);
        $attributeService->createNewAttributeValue('1vs1', $attributeKey, $subAttributeKey);
        $attributeService->createNewAttributeValue('Team', $attributeKey, $subAttributeKey);

        array_push($attributes, $attributeKey);
        return $attributes;
    }

    /**
     * @param AttributeKey[] $attributes
     *
     * @return InsertionType
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public static function createInsertionTypes($attributes) {
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $insertionType = $insertionTypeService->createNewInsertionType('eSports');

        foreach ($attributes as $attribute) {
            $insertionTypeService->addAttributeToInsertionType($attribute);
        }

        return $insertionType;
    }
    public static function createInsertions() {
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');
        $insertionService->createNewInsertion();
    }
    public static function createInsertionAttributeValues() {}
}
