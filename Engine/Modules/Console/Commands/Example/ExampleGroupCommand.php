<?php

namespace Oforge\Engine\Modules\Console\Commands\Example;

use Oforge\Engine\Modules\Console\Abstracts\AbstractGroupCommand;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class ExampleBatchCommand
 *
 * @package Oforge\Engine\Modules\Console\Commands\Development\Example
 */
class ExampleGroupCommand extends AbstractGroupCommand {

    /**
     * ExampleBatchCommand constructor.
     *
     * @throws ServiceNotFoundException
     */
    public function __construct() {
        parent::__construct('example:group', ['example:cmd2', 'example:cmd1' => '',], self::TYPE_DEVELOPMENT);
        $this->setDescription('Example group command. Will run subcommand cmd2 and cmd1.');
    }

}
