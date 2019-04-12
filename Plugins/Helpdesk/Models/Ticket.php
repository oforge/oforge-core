<?php

namespace Helpdesk\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Abstracts\AbstractModel;

/**
 * @ORM\Table(name="oforge_helpdesk_ticket")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Ticket extends AbstractModel {
    /**
     * @var int
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="ticket_opener", type="string", nullable=false)
     */
    private $opener;

    /**
     * @var string
     * @ORM\Column(name="ticket_status", type="string", nullable=false)
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\OneToOne(targetEntity="IssueTypes")
     * @ORM\JoinColumn(name="issue_type_id", referencedColumnName="id", nullable=false)
     * @ORM\Column(type="integer")
     */
    private $issueType;

    /**
     * @var string
     * @ORM\Column(name="ticket_title", type="string", nullable=false)
     */
    private $title;


    /**
     * @var string
     * @ORM\Column(name="first_message", type="text", nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false);
     */
    private $timestamp;

    /**
     * Triggered on insert
     *
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function onPrePersist() {
        $this->timestamp = new \DateTime("now");
    }

    public function onPreUpdate() {
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
    public function getOpener() : string {
        return $this->opener;
    }

    /**
     * @param string $opener
     *
     * @return Ticket
     */
    public function setOpener(string $opener) : Ticket {
        $this->opener = $opener;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() : string {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return Ticket
     */
    public function setStatus(string $status) : Ticket {
        $this->status = $status;
        return $this;
    }

    /**
     * @return int
     */
    public function getIssueType() : int {
        return $this->issueType;
    }

    /**
     * @param int $issueType
     *
     * @return Ticket
     */
    public function setIssueType(int $issueType) : Ticket {
        $this->issueType = $issueType;
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
     * @return Ticket
     */
    public function setTitle(string $title) : Ticket {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return Ticket
     */
    public function setMessage(string $message) : Ticket {
        $this->message = $message;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp() : \DateTime {
        return $this->timestamp;
    }
}