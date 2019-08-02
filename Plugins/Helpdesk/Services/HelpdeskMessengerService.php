<?php

namespace Helpdesk\Services;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Messenger\Abstracts\AbstractMessengerService;
use Messenger\Models\Conversation;
use ReflectionException;

class HelpdeskMessengerService extends AbstractMessengerService {

    /**
     * @param array $data
     *
     * @return Conversation
     * @throws ORMException
     * @throws ReflectionException
     */
    public function createNewConversation(array $data) {
        $conversation = new Conversation();

        $data['status'] = 'open';
        $data['requesterType'] = 1;
        $data['requestedType'] = 2;

        $conversation->fromArray($data);

        $this->entityManager()->create($conversation);

        parent::sendMessage($conversation->getId(), $data['requester'], $data['firstMessage']);

        return $conversation;
    }

    /**
     * @param $userId
     *
     * @return Collection
     */
    public function getConversationList($userId) {
        $queryBuilder = $this->entityManager()->createQueryBuilder();
        $query        = $queryBuilder
            ->select('c')
            ->from(Conversation::class, 'c')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requested',null),
                    $queryBuilder->expr()->eq('c.type','?1')))
            ->orWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.requested', $userId),
                    $queryBuilder->expr()->eq('c.type', '?1')))
            ->setParameters([1 => 'helpdesk_inquiry'])
            ->getQuery();
        $result = $query->execute();

        return $result;
    }
}
