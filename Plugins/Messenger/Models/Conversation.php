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
     * @var integer
     * @ORM\Column(name="user_reqeuster_type", type="integer", nullable=false)
     */
    private $requesterType;

    /**
     * @var integer
     * @ORM\Column(name="user_reqeusted_type", type="integer", nullable=false)
     */
    private $requestedType;

    /**
     * @var string
     * @ORM\Column(name="conversation_status", type="string", nullable=false)
     */
    private $status;

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
    private $lastMessage = null;

    /**
     * @var \DateTime
     * @ORM\Column(name="last_message_timestamp", type="datetime", nullable=true);
     */
    private $lastMessageTimestamp = null;

    /**
     * @var \DateTime
     * @ORM\Column(name="requester_last_seen", type="datetime", nullable=true);
     */
    private $requesterLastSeen = null;

    /**
     * @var \DateTime
     * @ORM\Column(name="requested_last_seen", type="datetime", nullable=true);
     */
    private $requestedLastSeen = null;

    /**
     * Triggered on insert
     *
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $this->id = hash('md5', $this->type . '_' . $this->requester . '_' . $this->requested . '_' . $this->targetId);
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
     * @return string $status
     */
    public function getStatus() : string {
        return $this->status;
    }

    /**
     * @param string
     *
     * @return Conversation
     */
    public function setStatus(string $status) : Conversation {
        $this->status = $status;

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
     * @return \DateTime|null
     */
    public function getLastMessageTimestamp() : ?\DateTime {
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
     * @return string|null
     */
    public function getLastMessage() : ?string {
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

    /**
     * @return int
     */
    public function getRequesterType() : int {
        return $this->requesterType;
    }

    /**
     * @param int $requesterType
     *
     * @return Conversation
     */
    public function setRequesterType(int $requesterType) : Conversation {
        $this->requesterType = $requesterType;

        return $this;
    }

    /**
     * @return int
     */
    public function getRequestedType() : int {
        return $this->requestedType;
    }

    /**
     * @param int $requestedType
     *
     * @return Conversation
     */
    public function setRequestedType(int $requestedType) : Conversation {
        $this->requestedType = $requestedType;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getRequesterLastSeen() : ?\DateTime {
        return $this->requesterLastSeen;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setRequesterLastSeen(\DateTime $timestamp)  {
        $this->requesterLastSeen = $timestamp;
    }

    /**
     * @return \DateTime|null
     */
    public function getRequestedLastSeen() : ?\DateTime {
        return $this->requestedLastSeen;
    }

    /**
     * @param \DateTime $timestamp
     */
    public function setRequestedLastSeen(\DateTime $timestamp) {
        $this->requestedLastSeen = $timestamp;
    }
}
