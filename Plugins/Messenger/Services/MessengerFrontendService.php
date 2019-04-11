<?php

namespace Messenger\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Messenger\Abstracts\AbstractMessengerService;
use Messenger\Models\Conversation;

class MessengerFrontendService extends AbstractMessengerService {

    /**
     * @param $requester
     * @param $targetId
     * @param $title
     * @param $firstMessage
     * @param $requested
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function createNewConversation($requester, $targetId, $title, $firstMessage, $requested) {
        $conversation = new Conversation();
        $conversation->setRequester($requester);
        $conversation->setRequested($requested);
        $conversation->setTargetId($targetId);
        $conversation->setTitle($title);
        $conversation->setState('open');
        $conversation->setType('classified_advert');

        $this->entityManager()->persist($conversation);
        $this->entityManager()->flush();

        parent::sendMessage($conversation->getId(), $requester, $firstMessage );
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
}
