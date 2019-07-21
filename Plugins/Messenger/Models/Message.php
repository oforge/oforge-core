<?php

namespace Messenger\Models;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_messenger_message")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Message extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="sender", type="string", nullable=false)
     */
    private $sender;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false);
     */
    private $timestamp;

    /**
     * @var string
     * @ORM\Column(name="conversation_id", type="string", nullable=false)
     */
    private $conversationId;

    /**
     * Triggered on insert
     *
     * @ORM\PrePersist
     * @throws Exception
     */
    public function onPrePersist() {
        $this->timestamp = new DateTime("now");
    }

    /**
     * @return int
     */
    public function getId() : int {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSender() : string {
        return $this->sender;
    }

    /**
     * @param string $sender
     */
    public function setSender(string $sender) : void {
        $this->sender = $sender;
    }

    /**
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message) : void {
        $this->message = $message;
    }

    /**
     * @return DateTime
     */
    public function getTimestamp() : DateTime {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getConversationId() : string {
        return $this->conversationId;
    }

    /**
     * @param string $conversationId
     *
     * @return Message
     */
    public function setConversationId(string $conversationId) : Message {
        $this->conversationId = $conversationId;

        return $this;
    }

}
