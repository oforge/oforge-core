<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Enums\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
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
     * @return InsertionType[]
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public static function createInsertionTypes($attributes) {
        $insertionTypes = [];
        /** @var InsertionTypeService $insertionTypeService */
        $insertionTypeService = Oforge()->Services()->get('insertion.type');
        $insertionType = $insertionTypeService->createNewInsertionType('eSports');

        foreach ($attributes as $attribute) {
            $insertionTypeService->addAttributeToInsertionType($attribute);
        }
        array_push($insertionTypes, $insertionType);
        return $insertionTypes;
    }

    /**
     * @param InsertionType $insertionType
     *
     * @return Insertion[]
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public static function createInsertions($insertionType) {

        $insertions = [];

        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');
        $insertion = $insertionService->createNewInsertion($insertionType, 'Some fancy Product Title', 'This is a good product.');

        array_push($insertions, $insertion);

        $insertion = $insertionService->createNewInsertion($insertionType, 'Some other cool product stuff', 'This is a good product too. Dont buy it');
        array_push($insertions, $insertion);

        return $insertions;
    }

    /**
     * @param $insertion
     * @param $attributeKey
     *
     * @throws ServiceNotFoundException
     */
    public static function createInsertionAttributeValues($insertion, $attributeKey) {
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');
    }
}
