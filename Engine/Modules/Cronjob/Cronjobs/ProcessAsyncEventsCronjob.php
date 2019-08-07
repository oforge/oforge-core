<?php

namespace Oforge\Engine\Modules\Cronjob\Cronjobs;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Annotation\ORM\Discriminator\DiscriminatorEntry;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

/**
 * Class ProcessAsyncEventsCronjob
 * @ORM\Entity
 * @DiscriminatorEntry()
 *
 * @package Oforge\Engine\Modules\Cronjob\Cronjobs
 */
class ProcessAsyncEventsCronjob extends CommandCronjob {

    public function __construct() {
        parent::__construct();
        $this->fromArray([
            'name'              => 'oforge:events:process-async',
            'title'             => 'Async events processing',
            'executionInterval' => 60,
            'command'           => 'oforge:events:process-async',
        ]);
    }

}
