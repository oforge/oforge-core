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
    public function getMessages($conversationId) {
        return $this->repository('messages')->findBy(['conversation_id' => $conversationId]);
    }

    /**
     * @param $targetId
     *
     * @return Conversation|object
     */
    public function getConversationByTarget($targetId) {
        return $this->repository('messages')->findOneBy(['target_id' => $targetId]);
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
    public function sendMessage($conversationId, $sender, $messageContent) {
        /** @var Conversation $conversation */
        $conversation = $this->repository('conversation')->findBy(['id' => $conversationId]);

        $recipient = "";
        if ($conversation->getRequested() === $sender) {
            $recipient = $recipient . $conversation->getRequester();
        } else {
            $recipient = $recipient . $conversation->getRequested();
        }

        $messageObject = new Message();
        $messageObject->setSender($sender);
        $messageObject->setRecipient($recipient);
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