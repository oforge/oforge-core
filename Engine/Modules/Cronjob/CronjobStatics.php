<?php

namespace Oforge\Engine\Modules\Cronjob;

use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Cronjob settings constants
 *
 * @package Oforge\Engine\Modules\Cronjob
 */
class CronjobStatics {
    /**
     * Relative path of cronjob file logs folder.
     */
    public const CRONJOB_LOGS_DIR = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . 'cronjob';
    /**
     * Absolute path of cronjob file logs folder.
     */
    public const CRONJOB_LOGS_DIR_ABS = ROOT_PATH . self::CRONJOB_LOGS_DIR;
    /**
     * Setting-key: logfile days
     */
    const SETTING_LOGFILE_DAYS = 'cronjob_logfile_days';

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

}
