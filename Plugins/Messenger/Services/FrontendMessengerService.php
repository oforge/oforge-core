<?php

namespace Messenger\Services;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Messenger\Abstracts\AbstractMessengerService;
use Messenger\Models\Conversation;
use Messenger\Models\Message;
use ReflectionException;

class FrontendMessengerService extends AbstractMessengerService {

    /**
     * @param array $data
     *
     * @return string
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ReflectionException
     */
    public function createNewConversation(array $data) {
        $conversation = new Conversation();

        $data['requesterType'] = 1;
        $data['requestedType'] = 1;
        $data['status']        = 'open';

        $conversation->fromArray($data);

        parent::entityManager()->create($conversation);
        $this->entityManager()->flush();

        parent::sendMessage($conversation->getId(), $data['requester'], $data['firstMessage']);

        return $conversation;
    }

    /**
     * @param $conversationId
     * @param $status
     *
     * @throws ORMException
     */
    public function changeStatus($conversationId, $status) {
        /** @var Conversation $conversation */
        $conversation = parent::getConversationById($conversationId);

        $conversation->setStatus($status);
        $this->entityManager()->update($conversation);
    }

    /**
     * @param $userId
     *
     * @return Collection|array
     * @throws ORMException
     */
    public function getConversationList($userId) {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $queryBuilder->setParameter('open', 'open');

        $query = $queryBuilder->select('c')->from(Conversation::class, 'c')->where($queryBuilder->expr()->andX($queryBuilder->expr()
                                                                                                                            ->eq('c.requester', $userId),
                $queryBuilder->expr()->eq('c.requesterType', '1'), $queryBuilder->expr()->eq('c.status', ':open')))->orWhere($queryBuilder->expr()
                                                                                                                                          ->andX($queryBuilder->expr()
                                                                                                                                                              ->eq('c.requested',
                                                                                                                                                                  $userId),
                                                                                                                                              $queryBuilder->expr()
                                                                                                                                                           ->eq('c.requestedType',
                                                                                                                                                               '1'),
                                                                                                                                              $queryBuilder->expr()
                                                                                                                                                           ->eq('c.status',
                                                                                                                                                               ':open')))
                              ->orderBy('c.lastMessageTimestamp', 'DESC')->getQuery();

        /** @var Conversation[] $conversations */
        $conversations = $query->execute();
        $result        = [];

        foreach ($conversations as $conversation) {
            $conversation                   = $conversation->toArray();
            $unreadMessages                 = $this->countUnreadMessages($conversation, $userId);
            $conversation['unreadMessages'] = $unreadMessages;
            if ($conversation['requested'] == $userId) {
                $conversation['chatPartner'] = $conversation['requester'];
            } else {
                $conversation['chatPartner'] = $conversation['requested'];
            }

            /** @var UserService $userService */ /** @var User $requester */
            /** @var User $requested */
            $userService = Oforge()->Services()->get('frontend.user.management.user');
            $requester = $userService->getUserById($conversation['requester']);
            $requested = $conversation['requested'] === 'helpdesk' ? null : $userService->getUserById($conversation['requested']);

            if ($conversation['type'] === 'helpdesk_inquiry'
                || ($requester !== null && $requester->isActive() && $requested !== null
                    && $requested->isActive())) {
                $result[] = $conversation;
            }
        }

        return $result;
    }

    /**
     * @param $conversationId
     *
     * @return array|null
     * @throws ORMException
     */
    public function getConversationById($conversationId) {
        $conversation = parent::getConversationById($conversationId);
        if (isset($conversation)) {
            return $conversation->toArray();
        }
    }

    /**
     * @param $conversationId
     * @param $userId
     *
     * @return object|null
     * @throws ORMException
     */
    public function getConversation($conversationId, $userId) {
        $conversation = $this->getConversationById($conversationId);
        if (isset($conversation)) {
            if ($conversation['requester'] == $userId) {
                $conversation['chatPartner'] = $conversation['requested'];
            } else {
                $conversation['chatPartner'] = $conversation['requester'];
            }
            $conversation['messages'] = $this->getMessagesOfConversation($conversationId);
        }

        return $conversation;
    }

    /**
     * @param $conversationId
     * @param $userId
     *
     * @throws ORMException
     */
    public function updateLastSeen($conversationId, $userId) {
        $conversation = parent::getConversationById($conversationId);
        if ($conversation->toArray()['requester'] == $userId) {
            $conversation->setRequesterLastSeen(new DateTime("now"));
        } else {
            $conversation->setRequestedLastSeen(new DateTime("now"));
        }
        $this->entityManager()->update($conversation);
    }

    /**
     * @param $requester
     * @param $requested
     * @param $conversationType
     * @param $targetId
     *
     * @return object
     * @throws ORMException
     */
    public function checkForConversation($requester, $requested, $conversationType, $targetId) {
        $conversation = $this->repository('conversation')->findOneBy([
            'requester' => $requester,
            'requested' => $requested,
            'type'      => $conversationType,
            'targetId'  => $targetId,
        ]);

        return $conversation;
    }

    /**
     * @param $targetId
     *
     * @return int
     */
    public function countConversationForSpecificInsertion($targetId) : int {
        return sizeof($this->repository('conversation')->findBy([
            'targetId'  => $targetId,
        ])) ?? 0;
    }


    /**
     * Counts user's unread messages
     *
     * @param array $conversation
     * @param $userId
     *
     * @return int
     */
    public function countUnreadMessages(array $conversation, $userId) {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $queryBuilder->setParameter('conversationId', $conversation['id']);

        $userLastSeen = $conversation['requester'] == $userId ? 'requesterLastSeen' : 'requestedLastSeen';

        /**
         * Conversation has no timestamp with user's last call
         * -> count all messages of chat-partner
         */
        if (!isset($conversation[$userLastSeen])) {
            if ($conversation['requester'] == $userId) {
                $queryBuilder->setParameter('user', $conversation['requester']);
            } else {
                $queryBuilder->setParameter('user', $conversation['requested']);
            }
            $queryBuilder->select('msg')->from(Message::class, 'msg')->where('msg.conversationId = :conversationId')->andwhere('msg.sender != :user')
                         ->setMaxResults(10);

            $result = $queryBuilder->getQuery()->getArrayResult();

            return count($result);
        }

        /**
         * Conversation has a timestamp with user's last call
         * -> count all newer messages
         */
        if ($conversation['requester'] == $userId) {
            $queryBuilder->setParameter('lastSeen', $conversation['requesterLastSeen']);
        } else {
            $queryBuilder->setParameter('lastSeen', $conversation['requestedLastSeen']);
        }
        $queryBuilder->select('msg')->from(Message::class, 'msg')->where('msg.conversationId = :conversationId')->andWhere('msg.timestamp > :lastSeen');
        $result = $queryBuilder->getQuery()->getArrayResult();

        return count($result);
    }

    /**
     * @param $userId
     *
     * @return bool
     */
    public function hasUnreadMessages($userId) : bool {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $queryBuilder->setParameter('open', 'open');

        $query = $queryBuilder->select('c')->from(Conversation::class, 'c')->where($queryBuilder->expr()->andX($queryBuilder->expr()
                                                                                                                            ->eq('c.requester', $userId),
                $queryBuilder->expr()->eq('c.requesterType', '1'), $queryBuilder->expr()->eq('c.status', ':open')))->orWhere($queryBuilder->expr()
                                                                                                                                          ->andX($queryBuilder->expr()
                                                                                                                                                              ->eq('c.requested',
                                                                                                                                                                  $userId),
                                                                                                                                              $queryBuilder->expr()
                                                                                                                                                           ->eq('c.requestedType',
                                                                                                                                                               '1'),
                                                                                                                                              $queryBuilder->expr()
                                                                                                                                                           ->eq('c.status',
                                                                                                                                                               ':open')))
                              ->getQuery();
        /** @var Conversation[] $conversations */
        $conversations = $query->execute();
        if (!isset($conversations)) {
            return false;
        } else {
            foreach ($conversations as $conversation) {
                $conversation   = $conversation->toArray();
                $unreadMessages = $this->countUnreadMessages($conversation, $userId);
                if ($unreadMessages > 0) {
                    return true;
                }
            }

            return false;
        }
    }
}
