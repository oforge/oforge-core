<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class ContentTypeManagementService
 *
 * @package Oforge\Engine\Modules\CMS\Services
 */
class ContentTypeManagementService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(['default' => ContentType::class, 'group' => ContentTypeGroup::class]);
    }

    /**
     * @param array $options
     *
     * @return int EntityID
     * @throws ORMException
     * @throws ConfigOptionKeyNotExistException
     */
    public function add(array $options) : int {
        $entity = $this->repository()->findOneBy(['name' => $options['name']]);
        if (!isset($entity)) {
            if ($this->isValid($options)) {
                if (isset($options['group'])) {
                    if (is_string($options['group'])) {
                        $options['group'] = $this->getGroup($options['group']);
                    }
                }
                if (isset($options['label'])) {
                    $defaults = $options['label'];
                    $labelKey = 'cms_content_type_label_' . $options['name'];
                    I18N::translate($labelKey, $defaults);
                    unset($options['label']);
                }
                if (isset($options['hint'])) {
                    $defaults = $options['hint'];
                    $labelKey = 'cms_content_type_hint_' . $options['name'];
                    I18N::translate($labelKey, $defaults);
                    $options['hint'] = $labelKey;
                }
                $entity = ContentType::create($options);

                $this->entityManager()->create($entity);
            }
        }

        return $entity->getId();
    }

    /**
     * @param $name
     *
     * @return ContentType
     * @throws ORMException
     */
    public function get($name) {
        /** @var ContentType $entry */
        $entry = $this->repository()->findBy(['name' => $name]);

        return $entry;
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ['name', 'path', 'group', 'icon', 'classPath'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }

        //Check if correct type are set
        $keys = ['icon', 'name', 'classPath', 'path'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type string.");
            }
        }
        //Check if correct type are set
        $keys = ['label', 'hint'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key]) && !is_array($options[$key])) {
                throw new InvalidArgumentException("I18N $key value should be of type string (english default) or array(language=>default).");
            }
        }

        return true;
    }

    /**
     * @param $groupName
     *
     * @return ContentTypeGroup
     * @throws ORMException
     */
    private function getGroup(string $groupName) : ContentTypeGroup {
        $entity = $this->repository('group')->findOneBy(['name' => $groupName]);
        if ($entity == null) {
            $entity = ContentTypeGroup::create(['name' => $groupName]);
            $this->entityManager()->create($entity);
        }

        return $entity;
    }

}
