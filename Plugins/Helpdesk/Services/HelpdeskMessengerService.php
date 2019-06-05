<?php

namespace Helpdesk\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Messenger\Abstracts\AbstractMessengerService;
use Messenger\Models\Conversation;

class HelpdeskMessengerService extends AbstractMessengerService {

    /**
     * @param $requester
     * @param null $requested
     * @param $conversationType
     * @param $targetId
     * @param $title
     * @param $firstMessage
     *
     * @return Conversation
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewConversation($requester, $requested, $conversationType, $targetId, $title, $firstMessage) {
        $conversation = new Conversation();
        $conversation->setRequester($requester);
        $conversation->setRequested($requested);
        $conversation->setTargetId($targetId);
        $conversation->setTitle($title);
        $conversation->setState('open');
        $conversation->setType($conversationType);

        $this->entityManager()->create($conversation);

        parent::sendMessage($conversation->getId(), 'frontend', $requester, $firstMessage);

        return $conversation;
    }

    /**
     * @param $userId
     *
     * @return Collection
     * @throws ORMException
     */
    public function getConversationList($userId) {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $query        = $queryBuilder
            ->select('c')
            ->from(Conversation::class, 'c')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requested',null),
                    $queryBuilder->expr()->eq('c.type','?1')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requested', $userId),
                    $queryBuilder->expr()->eq('c.type', '?1')))
            ->setParameters([1 => 'helpdesk_inquiry'])
            ->getQuery();
        $result = $query->execute();

        return $result;
    }
}
