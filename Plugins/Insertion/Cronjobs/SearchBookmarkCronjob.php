<?php

namespace Insertion\Cronjobs;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

/**
 * Class Reminder30DaysCronjob
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Insertion\Cronjobs
 */
class SearchBookmarkCronjob extends CommandCronjob {
    public function __construct() {
        parent::__construct();
        $this->fromArray([
            'name'              => 'oforge:insertion:searchBookmark',
            'title'             => 'Send notification mails for new insertions of search bookmark',
            'executionInterval' => 24 * 60 * 60,
            'command'           => 'oforge:plugin:insertion:searchBookmark',
        ]);
    }
}
