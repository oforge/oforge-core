<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;

class ContentTypeManagementService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct(['default' => ContentType::class]);
    }

    /**
     * @param array $options
     *
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function put(array $options) : void {
        $element = $this->repository()->findOneBy(["name" => strtolower($options["name"])]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                $entity = ContentType::create($options);
                $this->entityManager()->persist($entity);
                $this->entityManager()->flush();
            }
        }
    }

    /**
     * @param $name
     *
     * @throws ORMException
     *
     * @return ContentType
     */
    public function get($name) {

        /** @var ContentType $entry */
        $entry = $this->repository()->findBy(["name" => $name]);

        return $entry;
    }


    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ORMException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ["name", "path", "group", "icon", "description", "classPath"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistsException($key);
            }
        }

        //Check if correct type are set
        $keys = ["name", "path", "icon", "description", "classPath"];
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
            $entries  = $this->repository()->findBy(["parent" => $data["name"]], ['order' => 'ASC']);
            $children = $this->fill($entries);
            if (sizeof($children) > 0) {
                $data["children"] = $children;
            }
            array_push($result, $data);
        }

        return $result;
    }

}
