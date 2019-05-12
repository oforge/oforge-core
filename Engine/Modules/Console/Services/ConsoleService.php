<?php

namespace Oforge\Engine\Modules\Console\Services;

use GetOpt\ArgumentException;
use GetOpt\Command;
use GetOpt\GetOpt;
use GetOpt\Option;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Oforge\Engine\Modules\Console\Abstracts\AbstractCommand;
use Oforge\Engine\Modules\Console\ConsoleStatics;
use Oforge\Engine\Modules\Console\Lib\ConsoleRenderer;
use Oforge\Engine\Modules\Console\Lib\Input;
use Oforge\Engine\Modules\Console\Lib\Monolog\Formatter\ConsoleFormatter;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Helper\BinaryHelper;
use Oforge\Engine\Modules\Core\Models\Module\Module;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;

/**
 * Class ConsoleService
 *
 * @package Oforge\Engine\Modules\Console\Services
 */
class ConsoleService {
    /**
     * Log format without time part.
     */
    protected const LOG_FORMAT_NO_TIME = "%message% %context% %extra%\n";
    /**
     * Log format with time part.
     */
    protected const LOG_FORMAT_WITH_TIME = "[%datetime%] %message% %context% %extra%\n";
    /**
     * @var GetOpt $getOpt
     */
    protected $getOpt = null;
    /**
     * @var Logger $outputLogger
     */
    protected $outputLogger;
    /**
     * @var ConsoleFormatter $loggerFormatter
     */
    protected $loggerFormatter;
    /**
     * @var ConsoleRenderer $consoleRenderer
     */
    private $consoleRenderer;

    /**
     * ConsoleService constructor.
     */
    public function __construct() {
    }

    /**
     * Add handler to output logger.
     *
     * @param HandlerInterface $handler
     */
    public function addOutputLoggerHandler(HandlerInterface $handler) : void {
        $handler->setFormatter($this->loggerFormatter);
        $this->outputLogger->pushHandler($handler);
    }

    /**
     * Remove handler from output logger.
     *
     * @param HandlerInterface $handler
     */
    public function removeOutputLoggerHandler(HandlerInterface $handler) : void {
        $handlers = $this->outputLogger->getHandlers();
        $handlers = array_diff($handlers, [$handler]);
        $handlers = array_reverse($handlers);
        $this->outputLogger->setHandlers($handlers);
    }

    /**
     * @return ConsoleRenderer
     */
    public function getRenderer() : ConsoleRenderer {
        return $this->consoleRenderer;
    }

    /**
     * Get list of all (filtered) commands.
     * List can be filtered by CommandType.
     *
     * @see CommandType
     *
     * @param int $type Bitmask consisting of AbstractCommand type values, except AbstractCommand::TYPE_HIDDEN. [Default: AbstractCommand::TYPE_DEFAULT]
     *
     * @return \GetOpt\Command[]
     */
    public function getCommands(int $type = AbstractCommand::TYPE_DEFAULT) {
        if (is_null($this->getOpt)) {
            $this->init();
        }

        if (BinaryHelper::is($type, AbstractCommand::TYPE_HIDDEN)) {
            $type = AbstractCommand::TYPE_DEFAULT;
        }
        $commands = $this->getOpt->getCommands();
        if ($type !== AbstractCommand::ALL_TYPES) {
            $commands = array_filter($commands, function ($command) use ($type) {
                if (is_subclass_of($command, AbstractCommand::class)) {
                    /**
                     * @var AbstractCommand $command
                     */
                    return (BinaryHelper::is($command->getType(), $type));
                }

                return true;
            });
        }

        return $commands;
    }

    /**
     * Run command with argument string.
     *
     * @param string $command
     * @param string $args
     */
    public function runCommand(string $command, string $args = '') : void {
        $firstRun = false;
        if (is_null($this->getOpt)) {
            $this->init();
            $firstRun = true;
        }
        $getOpt = $this->getOpt;
        try {
            try {
                $getOpt->process(trim($command . ' ' . $args));
            } catch (ArgumentException\Missing $exception) {
                // catch missing exceptions if help is requested
                if (!$getOpt->getOption('help')) {
                    throw $exception;
                }
            }
        } catch (ArgumentException $exception) {
            file_put_contents('php://stderr', $exception->getMessage() . PHP_EOL);
            echo PHP_EOL, $this->consoleRenderer->renderHelp();
            exit();
        }
        $command = $getOpt->getCommand();
        if ($firstRun) {
            if ($getOpt->getOption('help') || !$command) {
                echo $this->consoleRenderer->renderHelp();
                exit();
            }
            $this->evaluateGlobalOptionLogtime();
            $this->evaluateGlobalOptionLogfile($command);
            $this->evaluateGlobalOptionVerbose();
        }
        $input = new Input($getOpt);
        // call the requested command
        call_user_func($command->getHandler(), $input, $this->outputLogger);
    }

    /**
     * Init ConsoleService.
     * Collect and create commands from modules and plugin.
     */
    protected function init() {
        $this->outputLogger    = new Logger('console');
        $this->loggerFormatter = new ConsoleFormatter(self::LOG_FORMAT_NO_TIME);
        try {
            $handler = new StreamHandler('php://stdout', Logger::DEBUG);
            $this->addOutputLoggerHandler($handler);
        } catch (\Exception $exception) {
            Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
        }
        $getOpt = new GetOpt(null, [
            \GetOpt\GetOpt::SETTING_STRICT_OPTIONS => false,
        ]);
        $getOpt->addOptions([
            Option::create(null, 'help', GetOpt::NO_ARGUMENT)#
                  ->setDescription('Display this help message'),
            Option::create('v', 'verbose', GetOpt::OPTIONAL_ARGUMENT)#
                  ->setDescription('Increase the verbosity of messages: 1 (v) for normal output, 2 (vv) for more verbose output and 3 (vvv) for debug')#
                  ->setDefaultValue(1),
            Option::create(null, 'logtime', GetOpt::NO_ARGUMENT)#
                  ->setDescription('Include time in in logger output [Default: none]'),
            Option::create(null, 'logfile', GetOpt::NO_ARGUMENT)#
                  ->setDescription('Include file handler in logger[Default: none]'),
        ]);
        $commands = $this->collectCommands();
        usort($commands, function ($c1, $c2) {
            /**
             * @var AbstractCommand $c1
             * @var AbstractCommand $c2
             */
            return strcmp($c1->getName(), $c2->getName());
        });
        $getOpt->addCommands($commands);
        $this->getOpt = $getOpt;

        $this->consoleRenderer = new ConsoleRenderer($this, $getOpt, function ($string) {
            return $string; // TODO use oforge translation function
        });
    }

    /**
     * Collect command class names of active modules and plugins.
     *
     * @return AbstractCommand[]
     */
    protected function collectCommands() {
        $commandInstances  = [];
        $commandClassNames = [];

        // TODO refactor after boostrap refactoring
        $entityManager    = Oforge()->DB()->getEntityManager();
        $moduleRepository = $entityManager->getRepository(Module::class);
        $pluginRepository = $entityManager->getRepository(Plugin::class);
        /**
         * @var Module[] $modules
         */
        $modules = $moduleRepository->findBy(['active' => 1], ['order' => 'ASC']);
        $moduleRepository->clear();
        foreach ($modules as $module) {
            $bootstrapName = $module->getName();
            /**
             * @var AbstractBootstrap $instance
             */
            $instance          = new $bootstrapName();
            $commandClassNames = array_merge($commandClassNames, $instance->getCommands());
        }
        /**
         * @var Plugin[] $plugins
         */
        $plugins = $pluginRepository->findBy(['active' => 1], ['order' => 'ASC']);
        $pluginRepository->clear();
        foreach ($plugins as $plugin) {
            $bootstrapName = $plugin->getName() . '\Bootstrap';
            /**
             * @var AbstractBootstrap $instance
             */
            $instance          = new $bootstrapName();
            $commandClassNames = array_merge($commandClassNames, $instance->getCommands());
        }

        foreach ($commandClassNames as $commandClassName) {
            if (is_subclass_of($commandClassName, AbstractCommand::class)) {
                $commandInstances[] = new $commandClassName();
            }
        }

        return $commandInstances;
    }

    /**
     * Evaluate global option --logtime.
     */
    protected function evaluateGlobalOptionLogtime() : void {
        if ($this->getOpt->getOption('logtime')) {
            $this->loggerFormatter->setFormat(self::LOG_FORMAT_WITH_TIME);
        }
    }

    /**
     * Evaluate global option --logfile.
     *
     * @param Command $command
     */
    protected function evaluateGlobalOptionLogfile(Command $command) : void {
        if ($this->getOpt->getOption('logfile') && !is_null($command)) {
            $filePath = ConsoleStatics::CONSOLE_LOGS_DIR_ABS . DIRECTORY_SEPARATOR . str_replace(':', '_', $command->getName()) . '.log';
            $maxFiles = 14;

            $this->addOutputLoggerHandler(new RotatingFileHandler($filePath, $maxFiles, Logger::DEBUG));
        }
    }

    /**
     * Evaluate global option --verbose|-v.
     */
    protected function evaluateGlobalOptionVerbose() : void {
        $verbose = $this->getOpt->getOption('verbose');
        if (!is_numeric($verbose)) {
            $verbose = 1 + substr_count($verbose, 'v');
        }
        switch ($verbose) {
            case 1:
                $loggerLevel = Logger::NOTICE;
                break;
            case 2:
                $loggerLevel = Logger::INFO;
                break;
            case 3:
            default:
                $loggerLevel = Logger::DEBUG;
                break;
        }
        $loggerHandlers = $this->outputLogger->getHandlers();
        foreach ($loggerHandlers as $loggerHandler) {
            if (is_subclass_of($loggerHandler, AbstractHandler::class)) {
                /**
                 * @var AbstractHandler $loggerHandler
                 */
                $loggerHandler->setLevel($loggerLevel);
            }
        }
    }

}
