<?php

namespace Helpdesk\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Helpdesk\Models\IssueTypes;
use Helpdesk\Models\Ticket;
use Messenger\Models\Conversation;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class HelpdeskTicketService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct([
            'default' => Ticket::class,
            'IssueTypes' => IssueTypes::class,
        ]);
    }

    /**
     * @param $opener
     * @param $issueType
     * @param $title
     * @param $message
     *
     * @return Conversation
     * @throws ServiceNotFoundException
     * @throws ORMException
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

        return $helpdeskMessengerService->createNewConversation($opener, 'helpdesk', 'helpdesk_inquiry', $ticket->getId(), $title, $message);
    }

    /**
     * @param string $status
     *
     * @return array|null
     * @throws ORMException
     */
    public function getTickets($status = "open") {
        return $this->repository()->findBy(['status' => $status]);
    }

    /**
     * @param $id
     *
     * @return Ticket|null
     * @throws ORMException
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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function changeStatus($id, $status) {
        $ticket = $this->getTicketById($id);

        $ticket->setStatus($status);

        $this->entityManager()->persist($ticket);
        $this->entityManager()->flush();
    }

    /**
     * @param $issueName
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createIssueType($issueName) {
        $issueType = new IssueTypes();
        $issueType->setIssueTypeName($issueName);

        $this->entityManager()->persist($issueType);
        $this->entityManager()->flush();
    }

    /**
     * @return array
     * @throws ORMException
     */
    public function getIssueTypes() {
        return $this->repository('IssueTypes')->findAll();
    }

}
