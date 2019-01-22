<?php

namespace Oforge\Engine\Modules\Console;

use Oforge\Engine\Modules\Core\Helper\Statics;

/**
 * Class ConsoleStatics
 *
 * @package Oforge\Engine\Modules\Console
 */
class ConsoleStatics {
    /**
     * Relative path of console file logs folder.
     */
    public const CONSOLE_LOGS_DIR = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . 'console';
    /**
     * Absolute path of console file logs folder.
     */
    public const CONSOLE_LOGS_DIR_ABS = ROOT_PATH . self::CONSOLE_LOGS_DIR;

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

}
