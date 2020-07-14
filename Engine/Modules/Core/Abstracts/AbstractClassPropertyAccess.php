<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

use Doctrine\Common\Collections\Collection;
use ReflectionMethod;

/**
 * Class AbstractModelAccess for class property access by fromArray and toArray
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractClassPropertyAccess {

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
     * @param array $whitelist
     *
     * @return $this
     */
    public function fromArray(array $array = [], array $whitelist = []) {
        $hasWhitelist = !empty($whitelist);
        $whitelist    = array_fill_keys($whitelist, 1);
        foreach ($array as $propertyName => $value) {
            if ($hasWhitelist && !isset($whitelist[$propertyName])) {
                continue;
            }
            $propertyName = ucfirst( $propertyName );
            if (strpos($propertyName, '_') !== false) {
                $propertyName = implode('', array_map('ucfirst', explode('_', $propertyName)));
                if ($hasWhitelist && !isset($whitelist[$propertyName])) {
                    continue;
                }
            }
            if ($hasWhitelist && !isset($whitelist[$propertyName])) {
                continue;
            }
            $getMethodName = 'get' . $propertyName;
            $setMethodName = 'set' . $propertyName;
            if (method_exists($this, $setMethodName)) {
                $reflectionMethod = new ReflectionMethod(static::class, $setMethodName);
                $parameters       = $reflectionMethod->getParameters();
                if (!empty($parameters)) {
                    if ($value !== null) {
                        $reflectionClass = $parameters[0]->getClass();
                        if ($reflectionClass === null) {
                            $parameterType = '' . $parameters[0]->getType();
                            switch ($parameterType) {
                                case 'int':
                                case 'bool':
                                case 'float':
                                    $convertMethod = $parameterType . 'val';
                                    $value         = $convertMethod($value);
                                    break;
                            }
                        } else {
                            $className = $reflectionClass->getName();
                            if ($className !== null) {
                                if (is_subclass_of($className, AbstractModel::class)) {
                                    $value = Oforge()->DB()->getForgeEntityManager()->getRepository($className)->find($value);
                                } elseif (is_subclass_of($className, AbstractClassPropertyAccess::class)) {
                                    if (is_array($value)) {
                                        $valueArray = $value;
                                        $value = $this->$getMethodName();
                                        if ($value === null) {
                                            $value = new $className();
                                        }
                                        /** @var AbstractClassPropertyAccess $value */
                                        $value->fromArray($valueArray);
                                    }
                                }
                            }
                        }
                    }
                    $this->$setMethodName($value);
                }
            } elseif (is_array($value) && method_exists($this, $getMethodName)) {
                $reflectionMethod = new ReflectionMethod(static::class, $getMethodName);
                $methodType       = "" . $reflectionMethod->getReturnType();
                if (class_exists($methodType) && is_subclass_of($methodType, AbstractClassPropertyAccess::class)) {
                    /** @var AbstractClassPropertyAccess $object */
                    $object = $this->$getMethodName();
                    if ($object !== null) {
                        $object->fromArray($value);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Convert this object to array.<br>
     * With the second parameter, properties can be excluded form convert.<br>
     * Example:<br>
     *      ['prop1', 'prop2', 'prop3' => ['sub31', 'sub32'], 'prop4' => ['*', '!sub41'] ]
     * <ul>
     *   <li>Excludes <i>prop1</i> and <i>prop2</i> of this object object.</li>
     *   <li>Excludes sub properties <i>sub31</i> and <i>sub32</i> of this object property <i>prop3</i>.</li>
     *   <li>Excludes all properties except <i>sub41</i> of current this object <i>prop4</i>.</li>
     * </ul>
     *
     * @param int $maxDepth
     * @param array $excludeProperties
     *
     * @return array
     */
    public function toArray($maxDepth = 2, $excludeProperties = []) : array {
        foreach ($excludeProperties as $key => $value) {
            if (is_numeric($key) && is_string($value)) {
                // unset($excludeProperties[$key]);
                $excludeProperties[$value] = $key;
            }
        }
        $result = [];
        foreach (get_class_methods($this) as $classMethod) {
            foreach (['get', 'is'] as $prefix) {
                $length = strlen($prefix);
                if (substr($classMethod, 0, $length) === $prefix) {
                    $propertyName = lcfirst(substr($classMethod, $length));
                    if (isset($excludeProperties[$propertyName])) {
                        if (is_array($excludeProperties[$propertyName])) {
                            $result[$propertyName] = $this->assignArray($this->$classMethod(), $maxDepth, $excludeProperties[$propertyName]);
                        }
                    } elseif (!isset($excludeProperties['*']) || isset($excludeProperties['!' . $propertyName])) {
                        $result[$propertyName] = $this->assignArray($this->$classMethod(), $maxDepth, []);
                    }

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
     * @param array $excludeProperties
     *
     * @return mixed
     */
    private function assignArray($result, int $maxDepth, array $excludeProperties) {
        if (is_scalar($result)) {
            return $result;
        } elseif (is_subclass_of($result, AbstractClassPropertyAccess::class)) {
            /** @var AbstractModel $result */
            if ($maxDepth > 0) {
                return $result->toArray($maxDepth - 1, $excludeProperties);
            } elseif (method_exists($result, 'getId')) {
                return $result->getId();
            }

            return null;
        } elseif (is_array($result) || is_subclass_of($result, Collection::class)) {
            $subResult = [];
            foreach ($result as $key => $item) {
                $subResult[$key] = $this->assignArray($item, $maxDepth - 1, $excludeProperties);
            }

            return $subResult;
        }

        return $result;
    }

}
