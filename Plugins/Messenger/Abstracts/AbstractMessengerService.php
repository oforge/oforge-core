<?php

namespace Messenger\Abstracts;

use Exception;
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
     * @param $targetId
     * @param $title
     * @param $requested
     */
    public abstract function createNewConversation($requester, $targetId, $title, $firstMessage, $requested);

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
     */
    public function getMessagesOfConversation($conversationId) {
        return $this->repository('message')->findBy(['conversationId' => $conversationId]);
    }

    /**
     * @param $targetId
     *
     * @return Conversation|object
     */
    public function getConversationByTarget($targetId) {
        return $this->repository('conversation')->findOneBy(['targetId' => $targetId]);
    }

    /**
     * @param $targetId
     *
     * @return Conversation|object
     */
    public function getConversationById($conversationId) {
        return $this->repository('conversation')->findOneBy(['id' => $conversationId]);
    }

    /**
     * @param $conversationId
     * @param $sender
     * @param $message
     *
     * @throws Exception
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
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeConversationState($conversationId, $newStatus) {
        /** @var Conversation $conversation */
        $conversation = $this->repository('conversation')->findBy(['id' => $conversationId]);

        $conversation->setState($newStatus);
        $this->entityManager()->persist($conversation);
        $this->entityManager()->flush();
    }

}