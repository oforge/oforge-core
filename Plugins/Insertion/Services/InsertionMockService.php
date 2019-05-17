<?php

namespace Insertion\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Insertion\Enum\AttributeType;
use Insertion\Models\AttributeKey;
use Insertion\Models\Insertion;
use Insertion\Models\InsertionAttributeValue;
use Insertion\Models\InsertionType;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class InsertionMockService {

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public static function init() {
        $attributes     = self::createAttributes();
        $insertionTypes = self::createInsertionTypes($attributes);
        $insertions     = [];

        foreach ($insertionTypes as $insertionType) {
            $insertions = array_merge($insertions, self::createInsertions($insertionType));
        }
        /** @var Insertion[] $insertions */
        foreach ($insertions as $insertion) {
            /** @var InsertionType $type */
            $type = $insertion->getInsertionType();
            /** @var AttributeKey[] $attributes */
            $attributes = $type->getAttributes();

            foreach ($attributes as $attribute) {
                self::createInsertionAttributeValues($insertion, $attribute, $attribute->getValues()->toArray()[0]);
            }
        }
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
        $insertionType        = $insertionTypeService->createNewInsertionType('eSports');

        foreach ($attributes as $attribute) {
            $insertionTypeService->addAttributeToInsertionType($insertionType, $attribute, false);
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
        $insertion        = $insertionService->createNewInsertion($insertionType, 'thelegend', 'This is a good player.');

        array_push($insertions, $insertion);

        $insertion = $insertionService->createNewInsertion($insertionType, 'noobmaster69', 'This is a flamer.');
        array_push($insertions, $insertion);

        return $insertions;
    }

    /**
     * @param $insertion
     * @param $attributeKey
     * @param $value
     *
     * @return InsertionAttributeValue
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public static function createInsertionAttributeValues($insertion, $attributeKey, $value) {
        /** @var InsertionService $insertionService */
        $insertionService        = Oforge()->Services()->get('insertion');
        $insertionAttributeValue = $insertionService->addAttributeValueToInsertion($insertion, $attributeKey, $value);

        return $insertionAttributeValue;
    }
}
