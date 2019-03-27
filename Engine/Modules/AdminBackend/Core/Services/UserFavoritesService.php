<?php

namespace Oforge\Engine\Modules\AdminBackend\Core\Services;

use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendUserFavorites;
use Oforge\Engine\Modules\AdminBackend\Core\Models\BackendNavigation;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Models\Endpoints\Endpoint;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;

class UserFavoritesService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            "default" => BackendUserFavorites::class,
            "navigation" => BackendNavigation::class
        ]);
    }

    public function toggle($userId, $routeName) {
        $instance = $this->repository()->findBy(["name" => $routeName, "userId" => $userId]);
        /** @var null|BackendNavigation $entry */
        $entry = $this->repository("navigation")->findOneBy(["name" => $routeName]);

        if (isset($instance) && sizeof($instance) > 0) {
            $instance[0]->setActive(!$instance[0]->isActive());
        } else {
            $instance = BackendUserFavorites::create(["name" => $routeName, "userId" => $userId, "active" => true]);
            $this->entityManager()->persist($instance);

            if (isset($entry)) {
                $instance->setIcon($entry->getIcon());
            }

            //TODO error handling

        }

        $this->entityManager()->flush();
    }

    public function isFavorite($userId, $routeName) : bool {
        $instance = $this->repository()->findBy(["name" => $routeName, "userId" => $userId, "active" => true]);

        return isset($instance) && sizeof($instance) > 0;
    }

    public function getAll($userId) : array {
        return $this->repository()->findBy(["userId" => $userId, "active" => true]);
    }
}