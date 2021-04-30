<?php

namespace Oforge\Engine\Modules\Core\Helper;

use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * Class DoctrineHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class DoctrineHelper
{

    /** Prevent instance. */
    public function __construct()
    {
    }

    /**
     * @param AbstractModel[] $entities
     * @param string $valueMethod
     * @param bool $removeDuplicates
     *
     * @return array
     */
    public static function propertyValues(array $entities, string $valueMethod, bool $removeDuplicates = false) : array
    {
        $result  = [];
        $checked = false;
        foreach ($entities as $entity) {
            if ( !$checked && !method_exists($entity, $valueMethod)) {
                throw new InvalidArgumentException("Value method '$valueMethod' does not exist.");
            }
            $checked  = true;
            $result[] = $entity->$valueMethod();
        }
        if ($removeDuplicates) {
            $result = array_unique($result);
        }

        return $result;
    }

    /**
     * @param AbstractModel[] $entities
     * @param string $keyMethod
     * @param string|null $valueMethod
     *
     * @return array
     */
    public static function map(array $entities, string $keyMethod, ?string $valueMethod = null) : array
    {
        $result  = [];
        $checked = false;
        foreach ($entities as $entity) {
            if ( !$checked) {
                if ( !method_exists($entity, $keyMethod)) {
                    throw new InvalidArgumentException("Key method '$keyMethod' does not exist.");
                }
                if ($valueMethod !== null && !method_exists($entity, $valueMethod)) {
                    throw new InvalidArgumentException("Value method '$valueMethod' does not exist.");
                }
            }
            $key   = $entity->$keyMethod();
            $value = $valueMethod === null ? $entity : $entity->$valueMethod();
            if ($checked || is_int($key) || is_string($key)) {
                $result[$key] = $value;
                $checked      = true;
            } else {
                throw new InvalidArgumentException("Key value of '$keyMethod' is no int or string.");
            }
        }

        return $result;
    }

    /**
     * @param AbstractModel[] $entities
     * @param int $maxDepth
     * @param array $excludeProperties
     *
     * @return array
     */
    public static function toArray(array $entities, int $maxDepth = 2, array $excludeProperties = []) : array
    {
        $result = [];
        foreach ($entities as $entity) {
            $result[] = $entity->toArray($maxDepth, $excludeProperties);
        }

        return $result;
    }

}
