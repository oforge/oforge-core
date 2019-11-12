<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

use Doctrine\Common\Collections\Collection;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class AbstractModel
 * (Database) Models from Modules or Plugins inherits from AbstractModel.
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractModel extends AbstractClassPropertyAccess {

    /**
     * Convert this object to array.
     *
     * @param int $maxDepth
     * @param array $cache
     *
     * @return array
     */
    public function toCleanedArray($maxDepth = 2, array &$cache = []) : array {
        $result = [];

        if (!is_array($result)) {
            $cache[get_class($result) . '::::' . $this->getId()] = true;
        }

        foreach (get_class_methods($this) as $classMethod) {
            foreach (['get', 'is'] as $prefix) {
                $length = strlen($prefix);
                if (substr($classMethod, 0, $length) === $prefix) {
                    $propertyName = lcfirst(substr($classMethod, $length));

                    $result[$propertyName] = $this->assignCleanedArray($this->$classMethod(), $maxDepth, $cache);
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Convert non scalar values to array.
     *
     * @param mixed $result
     * @param int $maxDepth
     * @param array $cache
     *
     * @return mixed
     */
    private function assignCleanedArray($result, int $maxDepth, array &$cache) {
        if (is_scalar($result)) {
            return $result;
        } elseif (is_subclass_of($result, AbstractModel::class)) {
            /** @var AbstractModel $result */
            if (method_exists($result, 'getId')) {
                if ($maxDepth > 0 && !isset($cache[get_class($result) . '::::' . $result->getId()])) {
                    $cache[get_class($result) . '::::' . $result->getId()] = true;

                    return $result->toCleanedArray($maxDepth - 1, $cache);
                } else {
                    return $result->getId();
                }

            }

            return null;
        } elseif (is_array($result) || is_subclass_of($result, Collection::class)) {
            $subResult = [];
            foreach ($result as $item) {
                $subResult[] = $item->assignCleanedArray($item, $maxDepth - 1, $cache);
            }

            return $subResult;
        }

        return $result;
    }

}
