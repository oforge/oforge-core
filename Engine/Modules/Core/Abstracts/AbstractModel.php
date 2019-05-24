<?php

namespace Oforge\Engine\Modules\Core\Abstracts;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\ORMException;
use ReflectionException;
use ReflectionMethod;

/**
 * Class AbstractModel
 * (Database) Models from Modules or Plugins inherits from AbstractModel.
 *
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractModel {

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

    /** @deprecated */
    public static function definition() {
        $methods = get_class_methods(static::class);
        $data    = [];
        foreach ($methods as $method) {
            $name = null;

            if (substr($method, 0, 3) === 'get') {
                $name = lcfirst(substr($method, 3));
            } elseif (substr($method, 0, 2) === 'is') {
                $name = lcfirst(substr($method, 2));
            }

            if (isset($name)) {
                $r = new ReflectionMethod(static::class, $method);

                $type = $r->getReturnType();

                $methodDefinition = ["name" => $name];
                if (isset($type)) {
                    $methodDefinition["type"] = "" . $type;
                }
                array_push($data, $methodDefinition);
            }
        }

        return $data;
    }

    /**
     * Method for mass-assignment.
     * Will call (existing) setter method for every key. Supports both key formats "testKey" and "test_key".
     *
     * @param array $array
     * @param array $fillable
     *
     * @return $this
     * @throws ORMException
     * @throws ReflectionException
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
                $r      = new ReflectionMethod(static::class, $method);
                $params = $r->getParameters();

                if (sizeof($params) == 1) {
                    $classObject = $params[0]->getClass();
                    if (isset($classObject)) {
                        $className = $classObject->getName();
                        if (isset($className)) {
                            $value = Oforge()->DB()->getEntityManager()->getRepository($className)->find($value);
                        }
                    } else {
                        switch ("" . $params[0]->getType()) {
                            case "int":
                                $value = intval($value);
                        }
                    }
                }

                $this->$method($value);
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
        } elseif (is_subclass_of($result, AbstractModel::class)) {
            /** @var AbstractModel $result */
            if ($maxDepth > 0) {
                return $result->toArray($maxDepth - 1, $excludeProperties);
            } elseif (method_exists($result, 'getId')) {
                return $result->getId();
            }

            return null;
        } elseif (is_array($result) || is_subclass_of($result, Collection::class)) {
            $subResult = [];
            foreach ($result as $item) {
                $subResult[] = $item->assignArray($item, $maxDepth - 1, $excludeProperties);
            }

            return $subResult;
        }

        return $result;
    }

    /**
     * Convert this object to array.
     *
     * @param int $maxDepth
     *
     * @return array
     */
    public function toCleanedArray($maxDepth = 2, array &$cache = []) : array {
        $result     = [];
        $cache[get_class($result) . "::::" . $this->getId()] = true;

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
     *
     * @return mixed
     */
    private function assignCleanedArray($result, int $maxDepth, array &$cache) {
        if (is_scalar($result)) {
            return $result;
        } elseif (is_subclass_of($result, AbstractModel::class)) {
            /** @var AbstractModel $result */
            if (method_exists($result, 'getId')) {
                if ($maxDepth > 0 && !isset($cache[get_class($result) . "::::" . $result->getId()])) {
                    $cache[get_class($result) . "::::" . $result->getId()] = true;

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
