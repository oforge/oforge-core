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
     * @var string
     * @ORM\Column(name="ticket_issue_type", type="string", nullable=false)
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
     * @return string
     */
    public function getIssueType() : string {
        return $this->issueType;
    }

    /**
     * @param string $issue
     *
     * @return Ticket
     */
    public function setIssueType(string $issueType) : Ticket {
        $this->issue = $issueType;
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