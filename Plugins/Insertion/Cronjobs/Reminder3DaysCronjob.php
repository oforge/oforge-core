<?php

namespace Insertion\Cronjobs;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Cronjob\Enums\ExecutionInterval;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

/**
 * Class Reminder3DaysCronjob
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Insertion\Cronjobs
 */
class Reminder3DaysCronjob extends CommandCronjob {

    public function __construct() {
        parent::__construct();
        $this->fromArray([
            'name'              => 'oforge:insertion:reminder3',
            'title'             => 'Insertion reminder for 3 days',
            'executionInterval' => ExecutionInterval::DAILY,
            'command'           => 'oforge:plugin:insertion:reminder --days 3',
        ]);
    }
}
