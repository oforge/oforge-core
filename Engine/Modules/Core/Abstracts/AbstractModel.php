<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

use Doctrine\ORM\PersistentCollection;

/**
 * Class AbstractModel
 * (Database) Models from Modules or Plugins inherits from AbstractModel.
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
class AbstractModel {

    /**
     * Fluent interface for constructor so methods can be called after construction.
     *
     * @param array $array
     * @param array $fillable optional property whitelist for mass-assignment
     *
     * @return static
     */
    public static function create(array $array = [], array $fillable = []) {
        $object = new static();
        $object->fromArray($array, $fillable);

        return $object;
    }

    /**
     * Method for mass-assignment.
     * Will call (existing) setter method for every key. Supports both key formats "testKey" and "test_key".
     *
     * @param array $array
     * @param array $fillable
     *
     * @return $this
     */
    public function fromArray(array $array = [], array $fillable = []) {
        foreach ($array as $key => $value) {
            if (count($fillable) && !in_array($key, $fillable)) {
                continue;
            }
            $keys   = explode("_", $key);
            $method = "set";

            foreach ($keys as $keyPart) {
                $method .= ucfirst($keyPart);
            }

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Convert this object to array.
     *
     * @param int $maxDepth
     *
     * @return array
     */
    public function toArray($maxDepth = 2) {
        $result = [];
        foreach (get_class_methods($this) as $classMethod) {
            foreach (['get', 'is'] as $prefix) {
                $length = strlen($prefix);
                if (substr($classMethod, 0, $length) === $prefix) {
                    $propertyName = lcfirst(substr($classMethod, $length));

                    $result[$propertyName] = $this->assignArray($this->$classMethod(), $maxDepth);
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
     *
     * @return mixed
     */
    private function assignArray($result, int $maxDepth) {
        if (is_scalar($result)) {
            return $result;
        } elseif (is_subclass_of($result, AbstractModel::class)) {
            /** @var AbstractModel $result */
            if ($maxDepth > 0) {
                return $result->toArray($maxDepth - 1);
            } elseif (method_exists($result, 'getId')) {
                return $result->getId();
            }

            return null;
        } elseif ((is_array($result) || is_a($result, PersistentCollection::class)) && $maxDepth > 0) {
            $subResult = [];
            foreach ($result as $item) {
                $subResult[] = $item->assignArray($item, $maxDepth - 1);
            }

            return $subResult;
        }

        return $result;
    }

}
