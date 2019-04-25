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
     * @return Collection|array
     * @throws ORMException
     */
    public function getConversationList($userId) {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $query        = $queryBuilder
            ->select('c')
            ->from(Conversation::class, 'c')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requester', $userId),
                    $queryBuilder->expr()->eq('c.type', '?1')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requested', $userId),
                    $queryBuilder->expr()->eq('c.type', '?2')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requester', $userId),
                    $queryBuilder->expr()->eq('c.type', '?3')))
            ->setParameters([1 => 'classified_advert', 2 => 'classified_advert', 3 => 'helpdesk_inquiry'])
            ->getQuery();
        /** @var Conversation[] $conversations */
        $conversations = $query->execute();
        $result = [];

        foreach($conversations as $conversation) {
            $conversation = $conversation->toArray();
            if ($conversation['requested'] == $userId) {
                $conversation['chatPartner'] = $conversation['requester'];
            } else {
                $conversation['chatPartner'] = $conversation['requested'];
            }
            array_push($result, $conversation);
        }

        return $result;
    }

    /**
     * @param $conversationId
     *
     * @return array
     * @throws ORMException
     */
    public function getConversationById($conversationId) {
        return parent::getConversationById($conversationId)->toArray();
    }

    /**
     * @param $conversationId
     * @param $userId
     *
     * @return array
     * @throws ORMException
     */
    public function getConversation($conversationId, $userId) {
        $conversation = $this->getConversationById($conversationId);
        if ($conversation['requester'] == $userId) {
            $conversation['chatPartner'] = $conversation['requested'];
        } else {
            $conversation['chatPartner'] = $conversation['requester'];
        }
        $conversation['messages'] = $this->getMessagesOfConversation($conversationId);
        return $conversation;
    }
}
