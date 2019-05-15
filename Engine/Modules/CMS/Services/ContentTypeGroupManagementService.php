<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\ContentTypes\Row;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;

class ContentTypeGroupManagementService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct(['default' => ContentTypeGroup::class]);
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
                $entity = ContentTypeGroup::create($options);
                $this->entityManager()->persist($entity);
                $this->entityManager()->flush();
            }
        }
    }

    /**
     * @param $name
     *
     * @return array Tree of SidebarNavigation data
     * @throws ORMException
     */
    public function get($name) {

        /** @var ContentTypeGroup[] $entries */
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
        $keys = ["name", "description"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistsException($key);
            }
        }

        return true;
    }
}
