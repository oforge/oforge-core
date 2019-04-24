<?php

namespace FrontendUserManagement\Services;

use FrontendUserManagement\Models\AccountNavigation;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;

class AccountNavigationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => AccountNavigation::class]);
    }

    /**
     * @param array $options
     *
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ParentNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function put(array $options) : void {
        $element = $this->repository()->findOneBy(["name" => strtolower($options["name"])]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                $entity = AccountNavigation::create($options);
                $this->entityManager()->persist($entity);
                $this->entityManager()->flush();
            }
        }
    }

    /**
     * @param $position
     *
     * @return array Tree of SidebarNavigation data
     */
    public function get($position) {
        //find all plugins order by "order"
        /** @var AccountNavigation[] $entries */
        $entries = $this->repository()->findBy(["parent" => 0, "position" => $position], ['order' => 'ASC']);
        $result  = $this->fill($entries);

        return $result;
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ParentNotFoundException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ["name"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistsException($key);
            }
        }

        // Check if the element is already within the system
        $element = $this->repository()->findOneBy(["name" => strtolower($options["name"])]);
        if (isset($element)) {
            throw new ConfigElementAlreadyExistsException(strtolower($options["name"]));
        }

        if (key_exists("parent", $options)) {
            $element = $this->repository()->findOneBy(["name" => $options["parent"]]);
            if (!isset($element)) {
                throw new ParentNotFoundException($options["parent"]);
            }
        }

        // Check if correct type are set
        if (isset($options["order"]) && !is_integer($options["order"])) {
            throw new \InvalidArgumentException("Required value should be of type integer. ");
        }

        // Check if correct type are set
        if (isset($options["position"]) && !is_string($options["position"])) {
            throw new \InvalidArgumentException("Required value should be of type string. ");
        }

        //Check if correct type are set
        $keys = ["title", "icon", "path"];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new \InvalidArgumentException("$key value should be of type string.");
            }
        }

        return true;
    }

    /**
     * @param AccountNavigation[] $entries
     *
     * @return array
     */
    private function fill($entries) {
        $result = [];

        foreach ($entries as $entry) {
            $data = $entry->toArray();
            /** @var AccountNavigation[] $entries */
            $entries  = $this->repository()->findBy(["parent" => $data["name"]], ['order' => 'ASC']);
            $children = $this->fill($entries);
            if (sizeof($children) > 0) {
                $data["children"] = $children;
            }
            array_push($result, $data);
        }

        return $result;
    }

    /**
     * @param string $activePath
     *
     * @return array
     */
    public function breadcrumbs($activePath) {
        $breadcrumbs = [];
        /** @var null|AccountNavigation $entry */
        $entry = $this->repository()->findOneBy(["path" => $activePath], ['order' => 'ASC']);

        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());

            if ($entry->getParent() != "0") {
                $this->findParents($entry, $breadcrumbs);
            }
        }

        return array_reverse($breadcrumbs);
    }

    /**
     * @param AccountNavigation $entry
     * @param array $breadcrumbs
     */
    private function findParents($entry, &$breadcrumbs) {
        /** @var null|AccountNavigation $entry */
        $entry = $this->repository()->findOneBy(["name" => $entry->getParent()], ['order' => 'ASC']);
        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());
        }

        if ($entry->getParent() != "0") {
            $this->findParents($entry, $breadcrumbs);
        }
    }
}
