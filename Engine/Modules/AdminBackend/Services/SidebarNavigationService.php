<?php

namespace Oforge\Engine\Modules\AdminBackend\Services;

use Oforge\Engine\Modules\AdminBackend\Models\SidebarNavigation;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;

class SidebarNavigationService
{
	/**
	 * @var \Doctrine\ORM\EntityManager $entityManager
	 */
	private $entityManager;
	/**
	 * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository $repository
	 */
	private $repository;

	public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository    = $this->entityManager->getRepository(SidebarNavigation::class);
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
	public function put(array $options) : void
    {
        $element = $this->repository->findOneBy([ "name" => strtolower($options["name"])]);
        if (!isset($element)) {
            if ($this->isValid($options)) {
                $entity = SidebarNavigation::create(SidebarNavigation::class, $options);
                $this->entityManager->persist($entity);
                $this->entityManager->flush();
            }
        }
    }

	/**
	 * @return array Tree of SidebarNavigation data
	 */
    public function get()
    {
        //find all plugins order by "order"
	    /** @var SidebarNavigation[] $entries */
	    $entries = $this->repository->findBy(array( "parent" => 0), array( 'order' => 'ASC'));
        $result  = $this->fill($entries);

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
	private function isValid($options)
    {
	    // Check if required keys are within the options
        $keys = ["name"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
	            throw new ConfigOptionKeyNotExists($key);
            }
        }

        // Check if the element is already within the system
        $element = $this->repository->findOneBy([ "name" => strtolower($options["name"])]);
        if (isset($element)) {
	        throw new ConfigElementAlreadyExists(strtolower($options["name"]));
        }

        if (key_exists("parent", $options)) {
            $element = $this->repository->findOneBy([ "name" => $options["parent"]]);
            if (!isset($element)) {
                throw new ParentNotFoundException($options["parent"]);
            }
        }

        // Check if correct type are set
        if (isset($options["order"]) && !is_integer($options["order"])) {
	        throw new \InvalidArgumentException("Required value should be of type integer. ");
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
	 * @param SidebarNavigation[] $entries
	 *
	 * @return array
	 */
	private function fill($entries)
    {
        $result = [];

        foreach ($entries as $entry) {
            $data = $entry->toArray();
	        /** @var SidebarNavigation[] $entries */
            $entries = $this->repository->findBy(array( "parent" => $data["name"]), array( 'order' => 'ASC'));
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
	public function breadcrumbs($activePath)
    {
        $breadcrumbs = [];
	    /** @var null|SidebarNavigation $entry */
	    $entry = $this->repository->findOneBy(array( "path" => $activePath), array( 'order' => 'ASC'));

        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());

            if ($entry->getParent() != "0") {
                $this->findParents($entry, $breadcrumbs);
            }
        }

        return array_reverse($breadcrumbs);
    }

	/**
	 * @param SidebarNavigation $entry
	 * @param array $breadcrumbs
	 */
	private function findParents($entry, &$breadcrumbs)
    {
	    /** @var null|SidebarNavigation $entry */
        $entry = $this->repository->findOneBy(array( "name" => $entry->getParent()), array( 'order' => 'ASC'));
        if (isset($entry)) {
            array_push($breadcrumbs, $entry->toArray());
        }

        if ($entry->getParent() != "0") {
            $this->findParents($entry, $breadcrumbs);
        }
    }


}
