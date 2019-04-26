<?php

namespace Messenger\Abstracts;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Messenger\Models\Conversation;
use Messenger\Models\Message;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

abstract class AbstractMessengerService extends AbstractDatabaseAccess {

    public function __construct() {
        parent::__construct([
            'conversation' => Conversation::class,
            'message'      => Message::class,
        ]);
    }

    /**
     * @param $requester
     * @param $requested
     * @param $conversationType
     * @param $targetId
     * @param $title
     * @param $firstMessage
     */
    public abstract function createNewConversation($requester, $requested, $conversationType, $targetId, $title, $firstMessage);

    /**
     * @param $userId
     *
     * @return mixed
     */
    public abstract function getConversationList($userId);

    /**
     * @param $conversationId
     *
     * @return Message|array
     * @throws ORMException
     */
    public function getMessagesOfConversation($conversationId) {
        return $this->repository('message')->findBy(['conversationId' => $conversationId], ['timestamp' => 'ASC']);
    }

    /**
     * @param $targetId
     * @param $userId
     *
     * @return array
     * @throws ORMException
     */
    public function getConversationsByTarget($targetId, $userId) {

        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $query        = $queryBuilder
            ->select('c')
            ->from(Conversation::class, 'c')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requester', '?1'),
                    $queryBuilder->expr()->eq('c.targetId', $targetId)))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requested', '?1'),
                    $queryBuilder->expr()->eq('c.targetId', $targetId)))
            ->setParameters([1 => $userId])
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
     * @return Conversation|object
     * @throws ORMException
     */
    public function getConversationById($conversationId) {
        return $this->repository('conversation')->findOneBy(['id' => $conversationId]);
    }

    /**
     * @param $conversationId
     * @param $senderType
     * @param $sender
     * @param $messageContent
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function sendMessage($conversationId, $senderType, $sender, $messageContent) {
        /** @var Conversation $conversation */
        $conversation = $this->repository('conversation')->findOneBy(['id' => $conversationId]);

        $messageObject = new Message();
        $messageObject->setSender($senderType . '_' . $sender);
        $messageObject->setMessage($messageContent);
        $messageObject->setConversationId($conversationId);
        $this->entityManager()->persist($messageObject);
        $this->entityManager()->flush();



        $conversation->setLastMessage($messageObject->getMessage());
        $conversation->setLastMessageTimestamp($messageObject->getTimestamp());
        $this->entityManager()->persist($conversation);
        $this->entityManager()->flush();
    }

    /**
     * @param $conversationId
     * @param $newStatus
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function changeConversationState($conversationId, $newStatus) {
        /** @var Conversation $conversation */
        $conversation = $this->repository('conversation')->findBy(['id' => $conversationId]);

        $conversation->setState($newStatus);
        $this->entityManager()->persist($conversation);
        $this->entityManager()->flush();
    }
}
