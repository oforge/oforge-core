<?php

namespace Messenger\Models;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_massenger_message")
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
     * @ORM\Column(name="recipient", type="string", nullable=false)
     */
    private $recipient;

    /**
     * @var string
     * @ORM\Column(name="message", type="string", nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false);
     */
    private $timestamp;

    /**
     * @var string
     * @ManyToOne(targetEntity="Conversation")
     * @JoinColumn(name="conversation_id", referencedColumnName="id")
     */
    private $conversationId;

    /**
     * Triggered on insert
     *
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $this->timestamp = new \DateTime("now");
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
    public function getRecipient() : string {
        return $this->recipient;
    }

    /**
     * @param string $recipient
     */
    public function setRecipient(string $recipient) : void {
        $this->recipient = $recipient;
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
     * @return \DateTime
     */
    public function getTimestamp() : \DateTime {
        return $this->timestamp;
    }

    /**
     * @return string
     */
    public function getConversationId() : string {
        return $this->conversationId;
    }

    /**
     * @param string $message
     */
    public function setConversationId(string $conversationId) : Message {
        $this->conversationId = $conversationId;
        return $this;
    }

}