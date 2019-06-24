<?php

namespace Oforge\Engine\Modules\Core\Manager\Services;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\Reader;
use Doctrine\DBAL\Schema\Index;
use Oforge\Engine\Modules\Core\Annotation\Cache\Cache;
use Oforge\Engine\Modules\Core\Annotation\Cache\CacheInvalidation;
use Oforge\Engine\Modules\Core\Exceptions\ServiceAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Class ServiceManager
 *
 * @package Oforge\Engine\Modules\Core\Manager\Services
 */
class ServiceManager {
    /**
     * @var ServiceManager $instance
     */
    protected static $instance = null;
    /**
     * @var array $services
     */
    protected $services = [];

    protected function __construct() {
    }

    /**
     * @return ServiceManager
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new ServiceManager();
        }

        return self::$instance;
    }

    /**
     * Find a specific service by name.
     *
     * @param $name
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function get($name) {
        if (isset($this->services[$name])) {
            return $this->services[$name];
        }

        throw new ServiceNotFoundException($name);
    }

    /**
     * Get all (unsorted) service names.
     *
     * @return string[]
     */
    public function getServiceNames() {
        return array_keys($this->services);
    }

    /**
     * Register an array of services. Array of name-classname-pairs.
     *
     * @param array $services
     *
     * @throws ServiceAlreadyExistException
     */
    public function register(array $services) {
        foreach ($services as $name => $className) {
            $this->registerService($name, $className);
        }
    }

    public function initCaching() {
        try {
            class_exists(Cache::class);
            class_exists(CacheInvalidation::class);

            $reader = new IndexedReader(new AnnotationReader());

            foreach ($this->services as $name => $instance) {
                $classAnnotation = $this->getClassAnnotation($reader, $instance);

                if ($classAnnotation != null && $classAnnotation->isEnabled()) {
                    $methods = $this->getMethodAnnotations($reader, $instance);

                    $newInstance = new class($instance, $methods) {
                        private $oldInstance;
                        private $cacheMethods;

                        public function __construct($oldInstance, $cacheMethods) {
                            $this->oldInstance  = $oldInstance;
                            $this->cacheMethods = $cacheMethods;
                        }

                        public function __call($functionName, $arguments) {
                            if (method_exists($this->oldInstance, $functionName)) {
                                if (isset($this->cacheMethods[$functionName]) && $this->cacheMethods[$functionName] != null) {
                                    $annotation = $this->cacheMethods[$functionName];

                                    if ($annotation instanceof Cache) {
                                        if (Oforge()->Cache()->exists($annotation->getSlot(), get_class($this->oldInstance), $functionName, $arguments)) {
                                            return Oforge()->Cache()->get($annotation->getSlot(),  get_class($this->oldInstance), $functionName, $arguments);
                                        } else {
                                            $result = call_user_func_array([$this->oldInstance, $functionName], $arguments);
                                            Oforge()->Cache()->set($annotation->getSlot(),  get_class($this->oldInstance), $functionName, $arguments, $result);

                                            return $result;
                                        }
                                    } elseif ($annotation instanceof Cache) {
                                        Oforge()->Cache()->cleanUp($annotation->getSlot());
                                    }
                                }

                                return call_user_func_array([$this->oldInstance, $functionName], $arguments);
                            }
                        }
                    };

                    $this->services[$name] = $newInstance;

                }
            }
        } catch (AnnotationException $e) {
        } catch (ReflectionException $exception) {
            Oforge()->Logger()->get()->addWarning('Reflection exception: ' . $exception->getMessage(), $exception->getTrace());
        }
    }

    /**
     * Register a specific service by name
     *
     * @param string $name
     * @param string $className
     *
     * @throws ServiceAlreadyExistException
     */
    protected function registerService(string $name, string $className) {
        if (isset($this->services[$name])) {
            if (get_class($this->services[$name]) === $className) {
                return;
            }
            throw new ServiceAlreadyExistException($name);
        }

        $this->services[$name] = new $className();
    }

    protected function __clone() {
    }

    /**
     * @param IndexedReader $reader
     * @param $instance
     *
     * @throws ReflectionException
     */
    private function getClassAnnotation(Reader $reader, $instance) {
        /** @var Cache $classAnnotation */
        $reflectionClass = new ReflectionClass(get_class($instance));

        return $reader->getClassAnnotation($reflectionClass, Cache::class);
    }

    /**
     * @param Reader $reader
     * @param $instance
     *
     * @return array
     * @throws ReflectionException
     */
    public function getMethodAnnotations(Reader $reader, $instance) {
        $class        = get_class($instance);
        $classMethods = get_class_methods(get_class($instance));

        if (is_null($classMethods)) {
            $classMethods = [];
        }

        $result = [];

        foreach ($classMethods as $classMethod) {
            $reflectionMethod = new ReflectionMethod($class, $classMethod);
            /** @var Cache $methodAnnotation */
            $methodAnnotation = $reader->getMethodAnnotation($reflectionMethod, Cache::class);

            $result[$classMethod] = null;
            if ($methodAnnotation != null) {
                $result[$classMethod] = $methodAnnotation;
            }

            $methodInvalidationAnnotation = $reader->getMethodAnnotation($reflectionMethod, CacheInvalidation::class);
            if ($methodInvalidationAnnotation != null) {
                $result[$classMethod] = $methodInvalidationAnnotation;
            }
        }

        return $result;
    }
}
