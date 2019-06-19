<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;

/**
 * Class ContentTypeGroupManagementService
 *
 * @package Oforge\Engine\Modules\CMS\Services
 */
class ContentTypeGroupManagementService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(ContentTypeGroup::class);
    }

    /**
     * @param array $options
     *
     * @return int ID of group.
     * @throws ORMException
     * @throws ConfigOptionKeyNotExistException
     */
    public function put(array $options) : int {
        $element = $this->repository()->findOneBy(['name' => strtolower($options['name'])]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                $element = ContentTypeGroup::create($options);
                $this->entityManager()->create($element);
            }
        }

        return $element->getId();
    }

    /**
     * @param $name
     *
     * @return array Tree of SidebarNavigation data
     * @throws ORMException
     */
    public function get($name) {
        /** @var ContentTypeGroup[] $entries */
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
        $keys = ['name', 'description'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }

        return true;
    }
}
