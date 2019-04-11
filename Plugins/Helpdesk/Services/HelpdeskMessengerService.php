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
     * @param $targetId
     * @param $title
     * @param $firstMessage
     * @param null $requested
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function createNewConversation($requester, $targetId, $title, $firstMessage, $requested = null) {
        $conversation = new Conversation();
        $conversation->setRequester($requester);
        $conversation->setRequested($requested);
        $conversation->setTargetId($targetId);
        $conversation->setTitle($title);
        $conversation->setState('open');
        $conversation->setType('helpdesk_inquiry');

        $this->entityManager()->persist($conversation);
        $this->entityManager()->flush();

        parent::sendMessage($conversation->getId(), 'frontend',$requester, $firstMessage);
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
            ->select(['conversations'])
            ->from(Conversation::class, 'conversations')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('requested',null),
                    $queryBuilder->expr()->eq('type','helpdesk_inquiry')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('requested', $userId),
                    $queryBuilder->expr()->eq('type', 'helpdesk_inquiry')))
            ->getQuery();

        return $query->execute();
    }
}
