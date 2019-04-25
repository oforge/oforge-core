<?php

namespace Messenger\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_messenger_conversation")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Conversation extends AbstractModel {
    /**
     * @var string
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="user_requester", type="string", nullable=false)
     */
    private $requester;

    /**
     * @var string
     * @ORM\Column(name="user_requested", type="string", nullable=false)
     */
    private $requested;

    /**
     * @var string
     * @ORM\Column(name="conversation_state", type="string", nullable=false)
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(name="conversation_type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(name="conversation_title", type="string", nullable=false)
     */
    private $title;

    /**
     * @var int
     * @ORM\Column(name="target_id", type="string", nullable=false)
     */
    private $targetId;

    /**
     * @var string
     * @ORM\Column(name="last_message", type="text", nullable=true);
     */
    private $lastMessage;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_message_timestamp", type="datetime", nullable=true);
     */
    private $lastMessageTimestamp;

    /**
     * Triggered on insert
     *
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $this->id = $this->type . '_' . $this->requester . '_' . $this->requested . '_' . $this->targetId;
    }

    /**
     * @return string
     */
    public function getId() : string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRequester() : string {
        return $this->requester;
    }

    /**
     * @param string $requester
     *
     * @return Conversation
     */
    public function setRequester(string $requester) : Conversation {
        $this->requester = $requester;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequested() : string {
        return $this->requested;
    }

    /**
     * @param string $requested
     *
     * @return Conversation
     */
    public function setRequested(string $requested) : Conversation {
        $this->requested = $requested;

        return $this;
    }

    /**
     * @return string
     */
    public function getState() : string {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return Conversation
     */
    public function setState(string $state) : Conversation {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Conversation
     */
    public function setType(string $type) : Conversation {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle() : string {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Conversation
     */
    public function setTitle(string $title) : Conversation {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getTargetId() : int {
        return $this->targetId;
    }

    /**
     * @param int $targetId
     *
     * @return Conversation
     */
    public function setTargetId(int $targetId) : Conversation {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastMessageTimestamp() : \DateTime {
        return $this->lastMessageTimestamp;
    }

    /**
     * @param \DateTime $lastMessageTimestamp
     *
     * @return Conversation
     */
    public function setLastMessageTimestamp(\DateTime $lastMessageTimestamp) : Conversation {
        $this->lastMessageTimestamp = $lastMessageTimestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastMessage() : string {
        return $this->lastMessage;
    }

    /**
     * @param string $lastMessage
     *
     * @return Conversation
     */
    public function setLastMessage(string $lastMessage) : Conversation {
        $this->lastMessage = $lastMessage;
        return $this;
    }


}
