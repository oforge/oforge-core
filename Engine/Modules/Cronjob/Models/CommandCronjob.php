<?php

namespace Oforge\Engine\Modules\Cronjob\Models;

use Doctrine\ORM\Mapping as ORM;
use Oforge\Engine\Modules\Core\Forge\OrmAnnotation\DiscriminatorEntry;

/**
 * Class CommandCronjob
 *
 * @ORM\Entity
 * @DiscriminatorEntry()
 * @package Oforge\Engine\Modules\Cronjob\Models
 */
class CommandCronjob extends AbstractCronjob {
    /**
     * @var string $title
     * @ORM\Column(name="command", type="string", nullable=false)
     */
    private $command;
    /**
     * @var string $title
     * @ORM\Column(name="command_args", type="string", nullable=false, options={"default":""})
     */
    private $commandArgs = '';

    /**
     * Cronjob constructor.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getCommand() : string {
        return $this->command;
    }

    /**
     * @param string $command
     *
     * @return CommandCronjob
     */
    public function setCommand(string $command) : CommandCronjob {
        if ($this->isEditable()) {
            $this->command = $command;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCommandArgs() : string {
        return $this->commandArgs;
    }

    /**
     * @param string|null $commandArgs
     *
     * @return CommandCronjob
     */
    public function setCommandArgs($commandArgs) : CommandCronjob {
        if ($this->isEditable()) {
            $this->commandArgs = is_null($commandArgs) ? '' : $commandArgs;
        }

        return $this;
    }

}
