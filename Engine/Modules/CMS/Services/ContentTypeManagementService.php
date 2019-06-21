<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;

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
    public function put(array $options) : int {
        $entity = $this->repository()->findOneBy(['name' => strtolower($options['name'])]);
        if (!isset($entity)) {
            if ($this->isValid($options)) {
                if (isset($options['group'])) {
                    if (is_string($options['group'])) {
                        $options['group'] = $this->getGroup($options['group']);
                    }
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
        $keys = ['name', 'path', 'group', 'icon', 'description', 'classPath'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }

        //Check if correct type are set
        $keys = ['name', 'path', 'icon', 'description', 'classPath'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type string.");
            }
        }

        return true;
    }

    /**
     * @param BackendNavigation[] $entries
     *
     * @return array
     * @throws ORMException
     */
    private function fill($entries) {
        $result = [];

        foreach ($entries as $entry) {
            $data = $entry->toArray();
            /** @var BackendNavigation[] $entries */
            $entries  = $this->repository()->findBy(['parent' => $data['name']], ['order' => 'ASC']);
            $children = $this->fill($entries);
            if (sizeof($children) > 0) {
                $data['children'] = $children;
            }
            array_push($result, $data);
        }

        return $result;
    }

    /**
     * @param $group
     *
     * @return ContentTypeGroup
     * @throws ORMException
     */
    private function getGroup($group) : ContentTypeGroup {
        $entity = $this->repository('group')->findOneBy(['name' => $group]);
        if ($entity == null) {
            $entity = ContentTypeGroup::create(['name' => $group, 'description' => '']);
            $this->entityManager()->create($entity);
        }

        return $entity;
    }

}
