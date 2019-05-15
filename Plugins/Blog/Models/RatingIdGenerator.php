<?php

namespace Blog\Models;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * Class RatingIdGenerator
 *
 * @package Blog\Models
 */
class RatingIdGenerator extends AbstractIdGenerator {

    /**@inheritDoc */
    public function generate(EntityManager $em, $entity) {
        /** @var Rating $entity */
        $now       = new DateTimeImmutable('now');
        $timestamp = $now->format('Ymd-His-u');
        $postID    = $entity->getPost()->getId();
        $userID    = $entity->getUserID();

        return "$postID-$userID-$timestamp";
    }

}
