<?php

namespace Oforge\Engine\Modules\Notifications\Services;

use Oforge\Engine\Modules\Notifications\Abstracts\AbstractNotificationService;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;

class BackendNotificationService extends AbstractNotificationService {

    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(BackendNotification::class);
    }

    /**
     * Returns array of user
     *
     * @param $userId
     *
     * @return array|object[]
     */
    public function getNotifications($userId) {
        return $this->repository->findBy(['userId' => $userId]);
    }

    /**
     * @param $userId
     * @param $type
     * @param $message
     * @param $link
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addNotification($userId, $type, $message, $link) {
        $notification = new BackendNotification();

        $notification->setUserId($userId);
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setLink($link);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /**
     * @param $id
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function markAsSeen($id) {
        $notification = $this->repository->find($id);

        $notification->setAsSeen();

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /**
     * @param $role
     *
     * @return array|object[]
     */
    public function getRoleNotifications($role) {
        return $this->repository->findBy(['group' => $role]);
    }

    /**
     * @param $role
     * @param $type
     * @param $message
     * @param $link
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addRoleNotification($role, $type, $message, $link) {
        $notification = new BackendNotification();

        $notification->setGroup($role);
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setLink($link);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
