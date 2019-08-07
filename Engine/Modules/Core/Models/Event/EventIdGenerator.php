<?php

namespace Oforge\Engine\Modules\Core\Models\Event;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * Class EventIdGenerator
 *
 * @package Oforge\Engine\Modules\Core\Models\Event
 */
class EventIdGenerator extends AbstractIdGenerator {

    /**@inheritDoc */
    public function generate(EntityManager $entityManager, $entity) {
        /** @var EventModel $entity */
        $now       = new DateTimeImmutable('now');
        $timestamp = $now->format('Y-m-d-H:i:s.u');
        $eventName = $entity->getEventName();

        return "$timestamp---$eventName";
    }

}
