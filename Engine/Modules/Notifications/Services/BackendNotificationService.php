<?php
/** @noinspection PhpHierarchyChecksInspection */

namespace Oforge\Engine\Modules\Notifications\Services;

use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Notifications\Abstracts\AbstractNotificationService;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;

class BackendNotificationService extends AbstractNotificationService
{

    public function __construct()
    {
        parent::__construct(BackendNotification::class);
    }

    /**
     * @param int $id
     *
     * @return BackendNotification|null
     */
    public function getNotificationById(int $id) : ?BackendNotification
    {
        /** @var BackendNotification|null $entity */
        $entity = $this->repository()->find($id);

        return $entity;
    }

    /**
     * @param int $userId
     * @param string $type
     * @param string $message
     * @param string|null $link
     *
     * @throws ORMException
     */
    public function addNotification(int $userId, string $type, string $message, ?string $link = null)
    {
        $link = empty($link) ? null : $link;
        $this->entityManager()->create(
            BackendNotification::create(
                [
                    'userId'  => $userId,
                    'type'    => $type,
                    'message' => $message,
                    'link'    => $link,
                ]
            )
        );
    }

    /**
     * @param int $role
     * @param string $type
     * @param string $message
     * @param string|null $link
     *
     * @throws ORMException
     */
    public function addRoleNotification(int $role, string $type, string $message, ?string $link = null)
    {
        $link = empty($link) ? null : $link;
        $this->entityManager()->create(
            BackendNotification::create(
                [
                    'role'    => $role,
                    'type'    => $type,
                    'message' => $message,
                    'link'    => $link,
                ]
            )
        );
    }

    /**
     * @param $role
     *
     * @return BackendNotification[]
     */
    public function getRoleNotifications($role) : array
    {
        /** @var BackendNotification[] $entities */
        $entities = $this->repository()->findBy(['role' => $role]);

        return $entities;
    }

    /**
     * Returns array of user
     *
     * @param int $userId
     * @param string $selector
     *
     * @return BackendNotification[]
     */
    public function getUserNotifications(int $userId, string $selector = AbstractNotificationService::ALL) : array
    {
        switch ($selector) {
            case AbstractNotificationService::ALL:
                $criteria = ['userId' => $userId];
                break;
            case AbstractNotificationService::SEEN;
                $criteria = ['userId' => $userId, 'seen' => true];
                break;
            case AbstractNotificationService::UNSEEN:
            default:
                $criteria = ['userId' => $userId, 'seen' => false];
        }
        /** @var BackendNotification[] $entities */
        $entities = $this->repository()->findBy($criteria);

        return $entities;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function markAsSeen(int $id) : bool
    {
        try {
            $notification = $this->repository()->find($id);
            if (isset($notification)) {
                $notification->setSeen(true);
                $this->entityManager()->update($notification);

                return true;
            }
        } catch (ORMException $exception) {
            Oforge()->Logger()->logException($exception);
        }

        return false;
    }
}
