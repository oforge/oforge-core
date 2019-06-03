<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendUserFavorites;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

/**
 * Class UserFavoritesService
 *
 * @package Oforge\Engine\Modules\AdminBackend\Core\Services
 */
class UserFavoritesService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            "default" => BackendUserFavorites::class,
            "navigation" => BackendNavigation::class
        ]);
    }

    /**
     * @param $userId
     * @param $routeName
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function toggle($userId, $routeName) {
        /** @var BackendUserFavorites[]|null $backendUserFavourites */
        $backendUserFavourites = $this->repository()->findBy(["name" => $routeName, "userId" => $userId]);
        /** @var null|BackendNavigation $backendNavigation */
        $backendNavigation = $this->repository("navigation")->findOneBy(["name" => $routeName]);

        if (isset($backendUserFavourites) && sizeof($backendUserFavourites) > 0) {
            $backendUserFavourites[0]->setActive(!$backendUserFavourites[0]->isActive());
        } else {
            $backendUserFavourites = BackendUserFavorites::create(["name" => $routeName, "userId" => $userId, "active" => true]);
            $this->entityManager()->create($backendUserFavourites, false);

            if (isset($backendNavigation)) {
                $backendUserFavourites->setIcon($backendNavigation->getIcon());
            }

            //TODO error handling

        }

        $this->entityManager()->flush();
    }

    /**
     * @param $userId
     * @param $routeName
     *
     * @return bool
     * @throws ORMException
     */
    public function isFavorite($userId, $routeName) : bool {
        $backendUserFavourites = $this->repository()->findBy(["name" => $routeName, "userId" => $userId, "active" => true]);

        return isset($backendUserFavourites) && sizeof($backendUserFavourites) > 0;
    }

    /**
     * @param $userId
     *
     * @return array
     * @throws ORMException
     */
    public function getAll($userId) : array {
        return $this->repository()->findBy(["userId" => $userId, "active" => true]);
    }

}
