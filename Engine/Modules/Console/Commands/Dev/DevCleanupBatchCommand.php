<?php

namespace Oforge\Engine\Modules\Console\Commands\Dev;

use Oforge\Engine\Modules\Console\Abstracts\AbstractBatchCommand;

/**
 * Class DevCleanupBatchCommand
 * Run all oforge:dev:cleanup:* cleanup commands.
 *
 * @package Oforge\Engine\Modules\Console\Commands\Dev
 */
class DevCleanupBatchCommand extends AbstractBatchCommand {

    /**
     * ExampleBatchCommand constructor.
     *
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct('oforge:dev:cleanup', [], self::TYPE_DEVELOPMENT);
        $this->setDescription('Run all oforge:dev:cleanup:* cleanup commands.');
    }

}
