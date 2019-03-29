<?php

namespace Oforge\Engine\Modules\I18n\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\I18n\Models\Language;
use ReflectionException;

/**
 * Class LanguageService
 *
 * @package Oforge\Engine\Modules\I18n\Services
 */
class LanguageService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => Language::class]);
    }

    /**
     * @param array $criteria
     *
     * @return array
     */
    public function list(array $criteria = []) {
        return $this->repository()->findBy($criteria);
    }

    /**
     * @param int $criteria
     *
     * @return ?Language
     */
    public function get(int $id) {
        return $this->repository()->findOneBy(['id' => $id]);
    }

    /**
     * @param array $options
     *
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(array $options) {
        if ($this->isValid($options, true)) {
            $language = Language::create($options);
            $this->entityManager()->persist($language);
            $this->entityManager()->flush($language);
        }
    }

    /**
     * @param array $options
     *
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function update(array $options) {
        if ($this->isValid($options, false)) {
            /** @var Language $language */
            $language = $this->get((int) $options['id']);
            if (!isset($language)) {
                throw new NotFoundException("Language with id '{$options['id']}' not found!");
            }
            if (isset($language)) {
                $language->fromArray($options);
                if (!$this->entityManager()->contains($language)) {
                    $this->entityManager()->merge($language);
                }
                $this->entityManager()->flush($language);
            }
        }
    }

    /**
     * @param int $id
     *
     * @throws NotFoundException
     * @throws ORMException
     */
    public function delete(int $id) {
        $language = $this->get($id);
        if (!isset($language)) {
            throw new NotFoundException("Language with id '$id' not found!");
        }
        $this->entityManager()->remove($language);
        $this->entityManager()->flush($language);
    }

    /**
     * @param $options
     * @param bool $checkExisting
     *
     * @return bool
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     */
    protected function isValid($options, $checkExisting) {
        // Check if required keys are within the options
        $keys = ['iso', 'name'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExists($key);
            }
        }
        if ($checkExisting) {
            // Check if the entity is already within the system
            $entity = $this->repository()->findOneBy(['iso' => strtolower($options['iso'])]);
            if (isset($entity)) {
                throw new ConfigElementAlreadyExists(strtolower($options['iso']));
            }
        }
        // Check if correct type are set
        $keys = ['iso', 'name'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type string.");
            }
        }

        return true;
    }

}
