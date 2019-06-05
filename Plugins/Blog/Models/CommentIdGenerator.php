<?php

namespace Blog\Models;

use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * Class PostIdGenerator
 *
 * @package Blog\Models
 */
class CommentIdGenerator extends AbstractIdGenerator {

    /**@inheritDoc */
    public function generate(EntityManager $em, $entity) {
        /** @var Comment $entity */
        $now       = new DateTimeImmutable('now');
        $timestamp = $now->format('Ymd-His-u');
        $postID    = $entity->getPost()->getId();
        $authorID  = $entity->getAuthor()->getId();

        return "$postID-$authorID-$timestamp";
    }

}
