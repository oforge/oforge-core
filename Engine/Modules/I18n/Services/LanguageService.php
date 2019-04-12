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
        parent::__construct(Language::class);
    }

    /**
     * @param array $criteria
     *
     * @return array
     * @throws ORMException
     */
    public function list(array $criteria = []) {
        return $this->repository()->findBy($criteria);
    }

    /**
     * @param int $id
     *
     * @return Language|null
     * @throws ORMException
     */
    public function get(int $id) {
        /** @var Language|null $language */
        $language = $this->repository()->findOneBy(['id' => $id]);

        return $language;
    }

    /**
     * Returns list of active languages, ordered by name.
     *
     * @return Language[]
     * @throws ORMException
     */
    public function getActiveLanguages() {
        return $this->repository()->findBy(['active' => true], ['name' => 'ASC']);
    }

    /**
     * @param $context
     *
     * @return string
     */
    public function getCurrentLanguage($context) {
        if (isset($context['meta'])
            && isset($context['meta']['route'])
            && isset($context['meta']['route']['assetScope'])
            && isset($context['meta']['route']['languageId'])
            && strtolower($context['meta']['route']['assetScope']) !== 'backend') {
            return $context['meta']['route']['languageId'];
        }
        if (isset($_SESSION) && isset($_SESSION['config']) && isset($_SESSION['config']['language'])) {
            return $_SESSION['config']['language'];
        }

        try {
            /** @var ?Language $language */
            $language = $this->repository()->findOneBy(['active' => true]);
            if (isset($language)) {
                return $language->getIso();
            }
        } catch (ORMException $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }

        return 'en';
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
     * @throws ORMException
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
