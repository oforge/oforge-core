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
        $timestamp = $now->format('Ymd-His-u');
        $eventName = $entity->getEventName();
        $rand      = md5(rand(0, 9));

        return "$timestamp-$eventName-$rand";
    }

}
