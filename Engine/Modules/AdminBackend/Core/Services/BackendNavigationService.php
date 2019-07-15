<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\Auth\Services\PermissionService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Helper\TreeHelper;
use Oforge\Engine\Modules\Core\Services\EndpointService;

/**
 * Class BackendNavigationService
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Services
 */
class BackendNavigationService extends AbstractDatabaseAccess {
    const KEY_ADMIN         = 'backend_admin';
    const KEY_CONTENT       = 'backend_content';
    const KEY_SYSTEM        = 'backend_system';
    const KEY_DOCUMENTATION = 'backend_documentation';

    /** @var array Root-navigation-config for admins */
    public const CONFIG_ADMIN = [
        'name'     => self::KEY_ADMIN,
        'order'    => 100,
        'position' => 'sidebar',
    ];
    /** @var array Root-navigation-config for content */
    public const CONFIG_CONTENT = [
        'name'     => self::KEY_CONTENT,
        'order'    => 2,
        'position' => 'sidebar',
    ];
    /** @var array Root-navigation-config for system-admins */
    public const CONFIG_SYSTEM = [
        'name'     => self::KEY_SYSTEM,
        'order'    => 999,
        'position' => 'sidebar',
    ];
    /** @var array Documentation root-navigation-config for system-admins */
    public const CONFIG_DOCUMENTATION = [
        'name'     => self::KEY_DOCUMENTATION,
        'parent'   => self::KEY_SYSTEM,
        'order'    => 999,
        'icon'     => 'fa fa-file-text-o',
        'position' => 'sidebar',
    ];
    /** @var array<string, array> $activeEndpointsData */
    private $activeEndpointsData;

    public function __construct() {
        parent::__construct(BackendNavigation::class);
    }

    /**
     * @param array $options
     *
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ParentNotFoundException
     * @throws ORMException
     */
    public function add(array $options) : void {
        /** @var BackendNavigation $entity */
        $entity = $this->repository()->findOneBy(['name' => $options['name']]);
        if (!isset($entity)) {
            if ($this->isValid($options)) {
                $options['active'] = true;// TODO remove later after bootstrap config refactoring

                $entity = BackendNavigation::create($options);
                $this->entityManager()->create($entity);
            }
        }
    }

    /**
     * @param array $options
     * @param bool $active
     *
     * @throws ORMException
     */
    public function setActive(array $options, bool $active) : void {
        /** @var BackendNavigation $entity */
        $entity = $this->repository()->findOneBy(['name' => $options['name']]);
        if (isset($entity)) {
            $entity->setActive($active);
            $this->entityManager()->update($entity);
        }
    }

    /**
     * @param array $options
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(array $options) : void {
        /** @var BackendNavigation $entity */
        $entity = $this->repository()->findOneBy(['name' => $options['name']]);
        if (isset($entity)) {
            $this->entityManager()->remove($entity);
        }
    }

    /**
     * @param string $position
     *
     * @return array
     */
    public function getNavigationTree(string $position) : array {
        $this->initActiveEndpointsData();
        try {
            /** @var PermissionService $permissionService */
            $permissionService = Oforge()->Services()->get('permissions');
            $user              = Oforge()->View()->get('user');
            /** @var BackendNavigation[] $entries */
            $entries = $this->repository()->findBy(['position' => $position, 'visible' => true, 'active' => true], ['order' => 'ASC']);
            $tree    = TreeHelper::modelArrayToTree($entries, 'parent', '0', 'name');
            $this->filterTreeByPermission($permissionService, $user, $tree);

            return $tree;
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);

            return [];
        }
        // TODO: Nested Topbar-Menus ???
    }

    /**
     * @param string $activePath
     *
     * @return array
     * @throws ORMException
     */
    public function getBreadcrump(string $activePath) : array {
        $breadcrumb = [];
        /** @var BackendNavigation|null $entry */
        $entry = $this->repository()->findOneBy(['path' => $activePath], ['order' => 'ASC']);
        if (isset($entry)) {
            $breadcrumb[] = $entry->toArray();
            $this->findParents($entry, $breadcrumb);
        }

        return array_reverse($breadcrumb);
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ParentNotFoundException
     */
    private function isValid($options) {
        // Check if required keys are within the options
        $keys = ['name'];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }
        if (isset($options['parent'])) {
            $element = $this->repository()->findOneBy(['name' => $options['parent']]);
            if (!isset($element)) {
                throw new ParentNotFoundException($options['parent']);
            }
        }
        //Check if correct type are set
        $keys = ['order'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_integer($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type integer.");
            }
        }
        $keys = ['active', 'visible'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_bool($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type bool.");
            }
        }
        $keys = ['icon', 'parent', 'path', 'position', 'title'];
        foreach ($keys as $key) {
            if (isset($options[$key]) && !is_string($options[$key])) {
                throw new InvalidArgumentException("$key value should be of type string.");
            }
        }

        return true;
    }

    /**
     * @param BackendNavigation $entry
     * @param array $breadcrumb
     *
     * @throws ORMException
     */
    private function findParents(BackendNavigation $entry, array &$breadcrumb) {
        if ($entry->getParent() !== '0') {
            /** @var BackendNavigation|null $entry */
            $entry = $this->repository()->findOneBy(['name' => $entry->getParent()], ['order' => 'ASC']);
            if (isset($entry)) {
                $breadcrumb[] = $entry->toArray();
            }
            $this->findParents($entry, $breadcrumb);
        }
    }

    private function initActiveEndpointsData() {
        if (isset($this->activeEndpointsData)) {
            return;
        }
        $this->activeEndpointsData = [];

        try {
            /** @var EndpointService $endpointService */
            $endpointService = Oforge()->Services()->get('endpoint');
            $endpoints       = $endpointService->getActiveEndpoints();
            foreach ($endpoints as $endpoint) {
                $this->activeEndpointsData[$endpoint->getName()] = [
                    'class'  => $endpoint->getControllerClass(),
                    'method' => $endpoint->getControllerMethod(),
                ];
            }
        } catch (Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     * Removes items if permission not allowed.<br/>
     * Uses Controller ensurePermission values.
     *
     * @param PermissionService $permissionService
     * @param array $user
     * @param array $array
     */
    private function filterTreeByPermission(PermissionService $permissionService, ?array $user, array &$array) {
        foreach ($array as $index => &$item) {
            $checkChildren = true;
            if (!empty($item['path'])) {
                if (isset($this->activeEndpointsData[$item['path']])) {
                    $endpointData = $this->activeEndpointsData[$item['path']];

                    $permissions = $permissionService->get($endpointData['class'], $endpointData['method']);

                    $invalid = $user === null
                               || !isset($user['role'])
                               || $user['role'] > $permissions['role']
                               || !isset($user['type'])
                               || $user['type'] !== $permissions['type'];
                    if ($invalid) {
                        unset($array[$index]);
                        $checkChildren = false;
                    }
                }

            }
            if ($checkChildren && isset($item['children'])) {
                if ($checkChildren) {
                    $this->filterTreeByPermission($permissionService, $user, $item['children']);
                }
                if (empty($item['children'])) {
                    unset($array[$index]);
                }
            }
        }
    }

}
