<?php

namespace Messenger\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Messenger\Abstracts\AbstractMessengerService;
use Messenger\Models\Conversation;

class FrontendMessengerService extends AbstractMessengerService {

    /**
     * @param $requester
     * @param $requested
     * @param $conversationType
     * @param $targetId
     * @param $title
     * @param $firstMessage
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createNewConversation($requester, $requested, $conversationType, $targetId, $title, $firstMessage) {
        $conversation = new Conversation();
        $conversation->setRequester($requester);
        $conversation->setRequested($requested);
        $conversation->setType($conversationType);
        $conversation->setState('open');
        $conversation->setTargetId($targetId);
        $conversation->setTitle($title);

        $this->entityManager()->persist($conversation);
        $this->entityManager()->flush();

        parent::sendMessage($conversation->getId(), $conversationType, $requester, $firstMessage);
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
            ->select(array('conversations'))
            ->from(Conversation::class, 'conversations')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('requester', $userId),
                    $queryBuilder->expr()->eq('type', 'classified_advert')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('requested', $userId),
                    $queryBuilder->expr()->eq('type', 'classified_advert')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('requester', $userId),
                    $queryBuilder->expr()->eq('type', 'helpdesk_inquiry')))
            ->getQuery();

        return $query->execute();
    }

    public function getConversationById($conversationId) {
        return parent::getConversationById($conversationId);
    }
}
