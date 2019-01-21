<?php

namespace Oforge\Engine\Modules\Console\Commands\Dev;

use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Core\Helper\FileSystemHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class DevDoctrineOrmCacheCleanupCommand
 *
 * @package Oforge\Engine\Modules\Console\Commands\Development
 */
class DevDoctrineOrmCacheCleanupCommand extends AbstractCommand {

    /**
     * DevDoctrineOrmCacheCleanupCommand constructor.
     */
    public function __construct() {
        parent::__construct('oforge:dev:cleanup:orm', self::TYPE_DEVELOPMENT);
        $this->setDescription('Remove doctrine orm cache folder');
    }

    /**
     * @inheritdoc
     */
    public function handle(Input $input, Logger $output) : void {
        $directory = ROOT_PATH . Statics::DB_CACHE_DIR;
        if (FileSystemHelper::delete($directory, true)) {
            $output->notice('Doctrine orm cache directory cleared.');
        } else {
            $output->notice('Doctrine orm cache directory could not be cleared.');
        }
    }

}
