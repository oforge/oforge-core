<?php

namespace Messenger\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Messenger\Abstracts\AbstractMessengerService;
use Messenger\Models\Conversation;
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
        $data['state'] = 'open';

        $conversation->fromArray($data);

        parent::entityManager()->create($conversation);
        $this->entityManager()->flush();

        parent::sendMessage($conversation->getId(), $data['requester'], $data['firstMessage']);

        return $conversation;
    }

    /**
     * @param $userId
     *
     * @return Collection|array
     */
    public function getConversationList($userId) {
        $queryBuilder = $this->entityManager()->createQueryBuilder();

        $query = $queryBuilder->select('c')->from(Conversation::class, 'c')
                    ->where(
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq('c.requester', $userId),
                            $queryBuilder->expr()->eq('c.requesterType', '1')))
                    ->orWhere(
                        $queryBuilder->expr()->andX(
                            $queryBuilder->expr()->eq('c.requested', $userId),
                            $queryBuilder->expr()->eq('c.requestedType', '1')))
                    ->orderBy('c.lastMessageTimestamp', 'DESC')
                    ->getQuery();

        /** @var Conversation[] $conversations */
        $conversations = $query->execute();
        $result        = [];

        foreach ($conversations as $conversation) {
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
}
