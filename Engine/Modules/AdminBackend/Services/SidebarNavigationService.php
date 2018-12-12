<?php

namespace Oforge\Engine\Modules\AdminBackend\Services;

use Oforge\Engine\Modules\AdminBackend\Models\SidebarNavigation;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;

class SidebarNavigationService
{
    public function __construct()
    {
        $this->em = Oforge()->DB()->getManager();
        $this->repo = $this->em->getRepository(SidebarNavigation::class);
    }

    public function put(Array $options)
    {
        $element = $this->repo->findOneBy(["name" => strtolower($options["name"])]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                $entity = SidebarNavigation::create(SidebarNavigation::class, $options);
                $this->em->persist($entity);
                $this->em->flush();
            }
        }
    }

    public function get()
    {
        //find all plugins order by "order"
        $entries = $this->repo->findBy(array("parent" => 0), array('order' => 'ASC'));
        $result = $this->fill($entries);

        return $result;
    }

    private function isValid($options)
    {
        /**
         * Check if required keys are within the options
         */
        $keys = ["name"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) throw new ConfigOptionKeyNotExists($key);
        }

        /**
         * Check if the element is already within the system
         */

        $element = $this->repo->findOneBy(["name" => strtolower($options["name"])]);
        if (isset($element)) throw new ConfigElementAlreadyExists(strtolower($options["name"]));

        if (key_exists("parent", $options)) {
            $element = $this->repo->findOneBy(["name" => $options["parent"]]);
            if (!isset($element)) {
                throw new ParentNotFoundException($options["parent"]);
            }
        }

        /**
         * Check if correct type are set
         */
        if (isset($options["order"]) && !is_integer($options["order"])) throw new \InvalidArgumentException("Required value should be of type integer. ");

        /**
         * Check if correct type are set
         */
        $keys = ["title", "icon", "path"];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) throw new \InvalidArgumentException("$key value should be of type string.");
        }
        return true;
    }

    private function fill($entries)
    {
        $result = [];

        foreach ($entries as $entry) {
            $data = $entry->toArray();
            $entries = $this->repo->findBy(array("parent" => $data["name"]), array('order' => 'ASC'));
            $children = $this->fill($entries);
            if (sizeof($children) > 0) {
                $data["children"] = $children;
            }
            array_push($result, $data);
        }

        return $result;
    }

    public function breadcrumbs($activePath)
    {
        $breadcrumbs = [];
        $entry = $this->repo->findOneBy(array("path" => $activePath), array('order' => 'ASC'));

        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());

            if ($entry->getParent() != "0") {
                $this->findParents($entry, $breadcrumbs);
            }
        }

        return array_reverse($breadcrumbs);
    }

    private function findParents($entry, &$breadcrumbs)
    {
        $entry = $this->repo->findOneBy(array("name" => $entry->getParent()), array('order' => 'ASC'));

        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());
        }

        if ($entry->getParent() != "0") {
            $this->findParents($entry, $breadcrumbs);
        }
    }


}