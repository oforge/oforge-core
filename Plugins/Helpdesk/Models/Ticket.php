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
    private $issue;

    /**
     * @var string
     * @ORM\Column(name="ticket_title", type="string", nullable=false)
     */
    private $title;


    /**
     * @var string
     * @ORM\Column(name="first_message", type="string", nullable=false)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="timestamp", type="datetime", nullable=false);
     */
    private $timestamp;
}