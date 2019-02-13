<?php

namespace Oforge\Engine\Modules\AdminBackend\Services;

use Oforge\Engine\Modules\AdminBackend\Models\BackendUserFavorites;
use Oforge\Engine\Modules\AdminBackend\Models\BackendNavigation;
use Oforge\Engine\Modules\Core\Models\Endpoints\Endpoint;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;

class UserFavoritesService
{
    /**
     * @var \Doctrine\ORM\EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository $repository
     */
    private $repository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository $navigationRepository
     */
    private $navigationRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository|\Doctrine\ORM\EntityRepository endpointsRepository
     */
    private $endpointsRepository;


    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(BackendUserFavorites::class);
        $this->navigationRepository = $this->entityManager->getRepository(BackendNavigation::class);
        $this->endpointsRepository = $this->entityManager->getRepository(Endpoint::class);
    }

    public function toggle($userId, $routeName)
    {


        $instance = $this->repository->findBy(["name" => $routeName, "userId" => $userId]);
        /** @var null|BackendNavigation $entry */
        $entry = $this->navigationRepository->findOneBy(["name" => $routeName]);

        if (isset($instance) && sizeof($instance) > 0) {
            $instance[0]->setActive(!$instance[0]->isActive());
        } else {
            $instance = BackendUserFavorites::create(["name" => $routeName, "userId" => $userId, "active" => true]);
            $this->entityManager->persist($instance);

            if (isset($entry)) {
                $instance->setIcon($entry->getIcon());
            }

            //TODO error handling

        }

        $this->entityManager->flush();
    }

    public function isFavorite($userId, $routeName): bool
    {
        $instance = $this->repository->findBy(["name" => $routeName, "userId" => $userId, "active" => true]);
        return isset($instance) && sizeof($instance) > 0;
    }

    public function getAll($userId): array
    {
        return $this->repository->findBy(["userId" => $userId, "active" => true]);
    }
}