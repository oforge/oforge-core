<?php

namespace Oforge\Engine\Modules\Console\Abstracts;

use GetOpt\Command;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Lib\Input;

/**
 * Class AbstractCommand
 *
 * @package Oforge\Engine\Modules\Console\Abstracts
 */
abstract class AbstractCommand extends Command {
    public const ALL_TYPES        = 0;
    public const TYPE_DEFAULT     = 1;
    public const TYPE_EXTENDED    = 2;
    public const TYPE_DEVELOPMENT = 4;
    public const TYPE_CRONJOB     = 8;
    public const TYPE_HIDDEN      = 16;
    /**
     * Separator for subcommands.
     */
    public const SUBCOMMAND_SEPARATOR = ':';
    /**
     * @var int $type Type of command.
     */
    private $type;

    /**
     * AbstractCommand constructor.
     *
     * @param string $name Name of command
     * @param int $type [Default: AbstractCommand::TYPE_DEFAULT]
     */
    public function __construct(string $name, int $type = self::TYPE_DEFAULT) {
        parent::__construct($name, [$this, 'handle']);
        $this->type = in_array($type, [
            self::TYPE_HIDDEN,
            self::TYPE_CRONJOB,
            self::TYPE_DEVELOPMENT,
            self::TYPE_EXTENDED,
            self::TYPE_DEFAULT,
        ]) ? $type : self::TYPE_DEFAULT;
    }

    /**
     * @return int
     */
    public function getType() : int {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return AbstractCommand
     */
    protected function setType(int $type) : AbstractCommand {
        $this->type = $type;

        return $this;
    }

    /**
     * Command handle function.
     *
     * @param Input $input
     * @param Logger $output
     */
    abstract public function handle(Input $input, Logger $output) : void;

}
