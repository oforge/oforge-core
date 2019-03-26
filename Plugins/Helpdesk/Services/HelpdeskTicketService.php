<?php

namespace Helpdesk\Services;

use Helpdesk\Models\Ticket;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

class HelpdeskTicketService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => Ticket::class,
        ]);
    }

    /**
     * @param $opener
     * @param $issueType
     * @param $title
     * @param $message
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function createNewTicket($opener, $issueType, $title, $message) {
        $ticket = new Ticket();
        $ticket->setIssueType($issueType);
        $ticket->setOpener($opener);
        $ticket->setStatus("open");
        $ticket->setTitle($title);
        $ticket->setMessage($message);

        $this->entityManager()->persist($ticket);
        $this->entityManager()->flush();

        /** @var HelpdeskMessengerService $helpdeskMessengerService */
        $helpdeskMessengerService = Oforge()->Services()->get("helpdesk.messenger");

        $helpdeskMessengerService->createNewConversation($opener, $ticket->getId(), $title, $message);
    }

    /**
     * @param string $status
     *
     * @return array|null
     */
    public function getTickets($status = "open") {
        return $this->repository()->findBy(['status' => $status]);
    }

    /**
     * @param $id
     *
     * @return Ticket|null
     */
    public function getTicketById($id) {
        /** @var Ticket $ticket */
        $ticket = $this->repository()->find($id);
        return $ticket;
    }

    /**
     * @param $id
     * @param $status
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeStatus($id, $status) {
        $ticket = $this->getTicketById($id);

        $ticket->setStatus($status);

        $this->entityManager()->persist($ticket);
        $this->entityManager()->flush();
    }

}