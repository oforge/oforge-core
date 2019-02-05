<?php

namespace Oforge\Engine\Modules\Notifications\Services;

use Oforge\Engine\Modules\Notifications\Abstracts\AbstractNotificationService;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;

class BackendNotificationService extends AbstractNotificationService {

    private $entityManager;
    private $repository;

    public function __construct() {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository    = $this->entityManager->getRepository(BackendNotification::class);
    }

    /**
     * Returns array of user
     *
     * @param $userId
     * @param string $selector
     *
     * @return array|object[]
     */
    public function getNotifications($userId, $selector = AbstractNotificationService::ALL) {
        switch ($selector) {
            case AbstractNotificationService::ALL:
                return $this->repository->findBy(['userId' => $userId]);
                break;
            case AbstractNotificationService::SEEN;
                return $this->repository->findBy(['userId' => $userId, 'seen' => 1]);
                break;
            case AbstractNotificationService::UNSEEN:
                return $this->repository->findBy(['userId' => $userId, 'seen' => 0]);
                break;
            default:
                return null;
        }
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
    public function addNotification($userId, $type, $message, $link = null) {
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

        $notification->setSeen(true);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }

    /**
     * @param $role
     *
     * @return array|object[]
     */
    public function getRoleNotifications($role) {
        return $this->repository->findBy(['role' => $role]);
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

        $notification->setRole($role);
        $notification->setType($type);
        $notification->setMessage($message);
        $notification->setLink($link);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
