<?php

namespace Insertion\Cronjobs;

use Oforge\Engine\Modules\Cronjob\Enums\ExecutionInterval;
use Oforge\Engine\Modules\Cronjob\Models\CommandCronjob;

class InsertionBookmarkReminderCronjob extends CommandCronjob {
    /**
     * SearchBookmarkCronjob constructor.
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \ReflectionException
     */
    public function __construct() {

        parent::__construct();
        $this->fromArray([
            'name'              => 'oforge:insertion:insertionBookmarkReminder',
            'title'             => 'Reminder for bookmarked insertions',
            'executionInterval' => ExecutionInterval::WEEKLY,
            'command'           => 'oforge:plugin:insertion:insertionBookmarkReminder',
        ]);
    }
}
