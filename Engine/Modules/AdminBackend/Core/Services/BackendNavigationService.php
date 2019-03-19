<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Services;

use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;

class BackendNavigationService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct(["default" => BackendNavigation::class]);
    }

    /**
     * @param array $options
     *
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ParentNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function put(array $options) : void {
        $element = $this->repository()->findOneBy(["name" => strtolower($options["name"])]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                $entity = BackendNavigation::create($options);
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
        /** @var BackendNavigation[] $entries */
        $entries = $this->repository()->findBy(["parent" => 0, "position" => $position], ['order' => 'ASC']);
        $result  = $this->fill($entries);

        // TODO: Nested Topbar-Menus
        return $result;
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigElementAlreadyExists
     * @throws ConfigOptionKeyNotExists
     * @throws ParentNotFoundException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ["name"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExists($key);
            }
        }

        // Check if the element is already within the system
        $element = $this->repository()->findOneBy(["name" => strtolower($options["name"])]);
        if (isset($element)) {
            throw new ConfigElementAlreadyExists(strtolower($options["name"]));
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
     * @param BackendNavigation[] $entries
     *
     * @return array
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

    /**
     * @param string $activePath
     *
     * @return array
     */
    public function breadcrumbs($activePath) {
        $breadcrumbs = [];
        /** @var null|BackendNavigation $entry */
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
     * @param BackendNavigation $entry
     * @param array $breadcrumbs
     */
    private function findParents($entry, &$breadcrumbs) {
        /** @var null|BackendNavigation $entry */
        $entry = $this->repository()->findOneBy(["name" => $entry->getParent()], ['order' => 'ASC']);
        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());
        }

        if ($entry->getParent() != "0") {
            $this->findParents($entry, $breadcrumbs);
        }
    }

}
