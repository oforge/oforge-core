<?php

namespace Oforge\Engine\Modules\Core\Services;

use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Models\Config\Config;
use Oforge\Engine\Modules\Core\Models\Config\Value;

/**
 * Class ConfigService
 *
 * @package Oforge\Engine\Modules\Core\Services
 */
class ConfigService extends AbstractDatabaseAccess {
    private $cache = [];

    /**
     * ConfigService constructor.
     */
    public function __construct() {
        parent::__construct(Config::class);
    }

    /**
     * Insert a config entry into the database<br/>Options keys:<br/>
     *      $config = [
     *          'name'      => '', # Required
     *          'type'      => ConfigType::..., # Required
     *          'group'     => '', # Required
     *          'default'   => ..., # Required
     *          'label'     => '', # Required
     *          'required'  => true | false # Optional, default = false
     *          'options'   => ['', ...],
     *          'value'     => ..., # Optional, default value with scope null if not set.
     *          'values'    => ..., # Array with keys scope (required) and value (optional, otherwise default value)
     *          'order'     => ..., # Optional, default = Statics::DEFAULT_ORDER
     *      ];
     *
     * @param array $options
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(array $options) {
        if ($this->isValid($options)) {
            $config = $this->getConfig($options['name']);
            if (is_null($config)) {
                $options['required'] = ArrayHelper::get($options, 'required', false);
                $defaultValue        = ArrayHelper::get($options, 'default', null);
                if (isset($options['values']) && is_array($options['values'])) {
                    $options['values'] = array_map(function ($entry) use ($defaultValue) {
                        return Value::create([
                            'value' => ArrayHelper::get($entry, 'value', $defaultValue),
                            'scope' => ArrayHelper::get($entry, 'scope'),
                        ]);
                    }, $options['values']);
                } else {
                    $options['values'] = [
                        Value::create([
                            'value' => ArrayHelper::get($options, 'value', $defaultValue),
                        ]),
                    ];
                }
                $config = Config::create($options);
                foreach ($config->getValues() as $value) {
                    $value->setConfig($config);
                }
                $this->entityManager()->create($config);
                $this->entityManager()->flush($config);
            }
            $this->repository()->clear();
        }
    }

    /**
     * Get a specific configuration value.
     *
     * @param string $name
     * @param string|null $scope
     *
     * @return mixed
     * @throws ConfigElementNotFoundException
     */
    public function get(string $name, ?string $scope = null) {
        if (isset($this->cache[$name][$scope])) {
            return $this->cache[$name][$scope];
        }
        $config = $this->getConfig($name);
        if (isset($config)) {
            foreach ($config->getValues() as $value) {
                if ($value->getScope() === $scope) {
                    $this->updateCacheValue($name, $scope, $value->getValue());

                    return $value->getValue();
                }
            }
        }
        throw new ConfigElementNotFoundException($name, $scope);
    }

    /**
     * Get distinct configuration group names.
     *
     * @return array
     * @throws ORMException
     */
    public function getConfigGroups() {
        return $this->repository()#
                    ->createQueryBuilder('c')#
                    ->select('c.group')#
                    ->distinct(true)#
        // ->addOrderBy('c.name')
                    ->getQuery()->getArrayResult();
    }

    /**
     * Get all configurations for group name.
     *
     * @param string $groupName
     *
     * @return Config[]
     * @throws ORMException
     */
    public function getGroupConfigs(string $groupName) {
        return $this->repository()->findBy(['group' => $groupName]);
    }

    /**
     * Remove configuration by name or if scope is set, only the scoped value.
     *
     * @param string $name
     * @param string|null $scope
     *
     * @throws ConfigElementNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(string $name, ?string $scope = null) {//TODO ungetestet
        $config = $this->getConfig($name);
        if (isset($config)) {
            if (isset($scope)) {
                foreach ($config->getValues() as $value) {
                    if ($value->getScope() === $scope) {
                        $this->entityManager()->remove($value, false);
                    }
                }
            } else {
                $this->entityManager()->remove($config, false);
            }
            $this->entityManager()->flush();
            $this->repository()->clear();
        } else {
            throw new ConfigElementNotFoundException($name, $scope);
        }
    }

    /**
     * Set a specific configuration value.
     *
     * @param string $name
     * @param mixed $value
     * @param string|null $scope
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws MappingException
     */
    public function update(string $name, $value, ?string $scope = null) : bool {
        $config = $this->getConfig($name);
        if (!isset($config)) {
            throw new ConfigElementNotFoundException($name, $scope);
        }
        foreach ($config->getValues() as $configValue) {
            if ($configValue->getScope() === $scope) {
                $this->updateCacheValue($name, $scope, $value);
                $configValue->setValue($value);
                $this->entityManager()->update($configValue);

                return true;
            }
        }
        throw new ConfigElementNotFoundException($name, $scope);
    }

    /**
     * Get configuration by name from database.
     *
     * @param string $name
     *
     * @return Config|null
     */
    protected function getConfig(string $name) : ?Config {
        /** @var Config|null $entity */
        try {
            $entity = $this->repository()->findOneBy(['name' => $name]);
        } catch (ORMException $e) {
            $entity = null;
        }

        return $entity;
    }

    /**
     * Check if the options are valid
     *
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     * @throws InvalidArgumentException
     */
    protected function isValid(array $options) {
        // Check if required keys are within the options
        $keys = ['name', 'group', 'label', 'default', 'type'];
        foreach ($keys as $key) {
            if (!isset($options[$key])) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }
        // Check if correct data type are set
        if (isset($options['required']) && !is_bool($options['required'])) {
            throw new InvalidArgumentException('Required value should be of type bool.');
        }
        if (isset($options['order']) && !is_integer($options['order'])) {
            throw new InvalidArgumentException('Position value should be of type integer.');
        }
        if (isset($options['options']) && !is_array($options['options'])) {
            throw new InvalidArgumentException('Options value should be of type array.');
        }
        $keys = ['name', 'label', 'description', 'group', 'type'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new InvalidArgumentException("Option '$key' value should be of type string.");
            }
        }
        // Check type values
        $types = ['boolean', 'string', 'password', 'number', 'integer', 'select'];
        $type  = $options['type'];
        if (!in_array($type, $types)) {
            throw new InvalidArgumentException("Type '$type' is not a valid type.");
        }

        return true;
    }

    private function updateCacheValue(string $name, ?string $scope, $value) {
        if (!isset($this->cache[$name])) {
            $this->cache[$name] = [];
        }
        $this->cache[$name][$scope] = $value;
    }

}
