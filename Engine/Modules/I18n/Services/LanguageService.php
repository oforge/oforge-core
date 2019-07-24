<?php

namespace Oforge\Engine\Modules\I18n\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use ReflectionException;

/**
 * Class LanguageService
 *
 * @package Oforge\Engine\Modules\I18n\Services
 */
class LanguageService extends AbstractDatabaseAccess {
    /** @var array $currentLanguageIso */
    private $currentLanguage;

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
     * @param bool $onlyActive
     *
     * @return array
     */
    public function getFilterDataLanguages(bool $onlyActive = false) : array {
        $languages = [];
        try {
            $criteria = [];
            if ($onlyActive) {
                $criteria['active'] = true;
            }
            /** @var Language[] $entities */
            $entities = $this->list($criteria);
            foreach ($entities as $entity) {
                $languages[$entity->getIso()] = $entity->getName();
            }
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return $languages;
    }

    /**
     * Get snippet count for languages.
     *
     * @return array
     */
    public function getFilterDataSnippetsOfLanguage() : array {
        $result       = [];
        $queryBuilder = $this->getRepository(Snippet::class)->createQueryBuilder('s');
        $entries      = $queryBuilder->select('s.scope, COUNT(s) as value')->groupBy('s.scope')->getQuery()->getArrayResult();
        foreach ($entries as $entry) {
            $result[$entry['scope']] = $entry['value'];
        }

        return $result;
    }

    /**
     * @param array $context
     *
     * @return string
     */
    public function getCurrentLanguageIso(array $context = []) {
        if (!isset($this->currentLanguage)) {
            if (isset($context['meta']['language']) && strtolower($context['meta']['route']['assetScope']) == 'frontend') {
                $this->currentLanguage = $context['meta']['language'];
                // }elseif (isset($context['meta']['route']['languageId']) // current endpoint has no language(id)
                //           && isset($context['meta']['route']['assetScope'])
                //           && strtolower($context['meta']['route']['assetScope']) !== 'backend') {
                //     $this->currentLanguageIso = $context['meta']['route']['languageId'];
            } elseif (isset($_SESSION['language'])) {
                $this->currentLanguage = $_SESSION['language'];
            } else {
                $this->updateCurrentLanguage();
            }
        }

        return $this->currentLanguage['iso'];
    }

    public function updateCurrentLanguage() {
        $this->currentLanguage = [
            'id'  => 1,
            'iso' => 'en',
        ];
        try {
            /** @var Language|null $language */
            $language = $this->repository()->findOneBy(['active' => true], ['default' => 'DESC']);
            if (isset($language)) {
                $this->currentLanguage = [
                    'id'  => $language->getId(),
                    'iso' => $language->getIso(),
                ];
            }
        } catch (ORMException $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }
        if (isset($_SESSION)) {
            $_SESSION['language'] = $this->currentLanguage;
        }
        Oforge()->View()->assign(['meta.language' => $this->currentLanguage]);
    }

    /**
     * @param array $options
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(array $options) {
        if ($this->isValid($options)) {
            $options['iso'] = strtolower($options['iso']);
            // Check if the entity is already within the system
            $language = $this->repository()->findOneBy(['iso' => $options['iso']]);
            if (!isset($language)) {
                $language = Language::create($options);
                $this->entityManager()->create($language);
            }
        }
    }

    /**
     * @param array $options
     *
     * @throws ConfigOptionKeyNotExistException
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function update(array $options) {
        if ($this->isValid($options)) {
            /** @var Language $language */
            $language = $this->get((int) $options['id']);
            if (!isset($language)) {
                throw new NotFoundException("Language with id '{$options['id']}' not found!");
            }
            if (isset($language)) {
                $language->fromArray($options);
                $this->entityManager()->update($language);
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
    }

    /**
     * @param $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    protected function isValid($options) {
        // Check if required keys are within the options
        $keys = ['iso', 'name'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
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
