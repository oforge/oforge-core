<?php

namespace Oforge\Engine\Modules\Cronjob\Cronjobs;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Cronjob\Enums\ExecutionInterval;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

/**
 * Class CronjobPing
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Oforge\Engine\Modules\Cronjob\Cronjobs
 */
class CronjobPing extends CommandCronjob {

    public function __construct() {
        parent::__construct();
        $this->fromArray([
            'name'              => 'oforge:cronjob:ping',
            'title'             => 'Ping if alive',
            'executionInterval' => ExecutionInterval::DAILY,
            'command'           => 'oforge:cronjob:ping',
        ]);
    }
}
