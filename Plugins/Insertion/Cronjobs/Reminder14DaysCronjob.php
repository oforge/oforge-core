<?php

namespace Insertion\Cronjobs;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

/**
 * Class Reminder14DaysCronjob
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Insertion\Cronjobs
 */
class Reminder14DaysCronjob extends CommandCronjob {

    public function __construct() {
        parent::__construct();
        $this->fromArray([
            'name'              => 'oforge:insertion:reminder14',
            'title'             => 'Insertion reminder for 14 days',
            'executionInterval' => 24 * 60 * 60,
            'command'           => 'oforge:plugin:insertion:reminder --days 14',
        ]);
    }
}