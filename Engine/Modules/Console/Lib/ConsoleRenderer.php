<?php

namespace Oforge\Engine\Modules\Console\Lib;

use GetOpt\Command;
use GetOpt\GetOpt;
use GetOpt\Operand;
use GetOpt\Option;
use Oforge\Engine\Modules\Console\Services\ConsoleService;

/**
 * Class ConsoleRenderer
 *
 * @package Oforge\Engine\Modules\Console\Lib
 */
class ConsoleRenderer {
    public const  DEFAULT_SCREEN_WIDTH           = 80;
    public const  SETTING_SCREEN_WIDTH           = 'screen_width';
    public const  SETTING_SCREEN_MAX_WIDTH       = 'screen_max_width';
    public const  SETTING_SHOW_OPTIONS           = 'show_options';
    public const  SETTING_SHOW_OPERANDS          = 'show_operands';
    public const  SETTING_SHOW_USAGE             = 'show_usage';
    public const  SETTING_SHOW_TITLE_COMMANDS    = 'show_title_commands';
    public const  SETTING_SHOW_TITLE_OPTIONS     = 'show_title_options';
    public const  SETTING_SHOW_TITLE_OPERANDS    = 'show_title_options';
    public const  SETTING_SHOW_TITLE_USAGE       = 'show_title_usage';
    public const  SETTING_SHOW_USAGE_DESCRIPTION = 'show_usage_description';
    private const TEXT_SURROUND_PLACEHOLDER      = '<>';
    private const TEXT_SURROUND_OPTIONAL         = '[]';
    private const TEXT_MULTIPLE                  = '...';
    /** @var array $settings */
    private $settings = [
        self::SETTING_SCREEN_MAX_WIDTH       => self::DEFAULT_SCREEN_WIDTH,
        self::SETTING_SHOW_OPTIONS           => true,
        self::SETTING_SHOW_OPERANDS          => true,
        self::SETTING_SHOW_USAGE             => true,
        self::SETTING_SHOW_TITLE_COMMANDS    => true,
        self::SETTING_SHOW_TITLE_OPTIONS     => true,
        self::SETTING_SHOW_TITLE_OPERANDS    => true,
        self::SETTING_SHOW_TITLE_USAGE       => true,
        self::SETTING_SHOW_USAGE_DESCRIPTION => true,
    ];
    /** @var ConsoleService $consoleService */
    private $consoleService;
    /** @var GetOpt $getOpt */
    private $getOpt;
    /** @var callable $translator */
    private $translator;

    /**
     * ConsoleRenderer constructor.
     *
     * @param ConsoleService $consoleService
     * @param GetOpt $getOpt
     * @param callable $translator
     */
    public function __construct(ConsoleService $consoleService, GetOpt $getOpt, callable $translator) {
        $this->consoleService = $consoleService;
        $this->getOpt         = $getOpt;
        $this->translator     = $translator;
    }

    /**
     * Render the help text
     *
     * @param array $config
     *
     * @return string
     */
    public function renderHelp($config = []) {
        $getOpt = $this->getOpt;
        foreach ($config as $setting => $value) {
            $this->settings[$setting] = $value;
        }
        $helpText = '';
        if ($this->settings[self::SETTING_SHOW_USAGE]) {
            $helpText .= $this->renderUsage();
        }
        if ($getOpt->hasOperands() && $this->settings[self::SETTING_SHOW_OPERANDS]) {
            $helpText .= $this->renderOperands($getOpt->getOperandObjects()) . PHP_EOL;
        }
        if ($getOpt->hasOptions() && $this->settings[self::SETTING_SHOW_OPTIONS]) {
            $helpText .= $this->renderOptions($getOpt->getOptionObjects()) . PHP_EOL;
        }
        $commands = $this->consoleService->getCommands();
        if ((count($commands) > 0) && !$getOpt->getCommand()) {
            $helpText .= $this->renderCommands($commands);
        }

        return $helpText;
    }

    /**
     * Render 2 column data. Use implode prepareColumns value with PHP_EOL.
     *
     * @param int $columnWidth
     * @param $data [column 0, column 1]
     *
     * @return string
     */
    public function renderColumns($columnWidth, $data) {
        $lines = $this->prepareColumns($columnWidth, $data);

        return implode(PHP_EOL, $lines);
    }

    /**
     * Prepare 2 column data
     *
     * @param int $columnWidth
     * @param $data [column 0, column 1]
     *
     * @return string[]
     */
    public function prepareColumns($columnWidth, $data) {
        $screenWidth = $this->getScreenWidth();

        foreach ($data as &$dataRow) {
            $text = '';
            $row  = sprintf('  % -' . $columnWidth . 's  %s', $dataRow[0], $dataRow[1]);

            while (mb_strlen($row) > $screenWidth) {
                $p = strrpos(substr($row, 0, $screenWidth), ' ');
                if ($p < $columnWidth + 4) {
                    // no space - check for dash
                    $p = strrpos(substr($row, 0, $screenWidth), '-');
                    if ($p < $columnWidth + 4) {
                        // break at screen width
                        $p = $screenWidth - 1;
                    }
                }
                $c    = substr($row, $p, 1);
                $text .= substr($row, 0, $p) . ($c !== ' ' ? $c : '') . PHP_EOL;
                $row  = sprintf('  %s  %s', str_repeat(' ', $columnWidth), substr($row, $p + 1));
            }
            $dataRow = $row;
        }

        return $data;
    }

    /**
     * @param Command[] $commands
     * @param bool $showTitle
     *
     * @return string
     */
    public function renderCommands($commands, $showTitle = true) {
        $data        = [];
        $columnWidth = 0;
        foreach ($commands as $command) {
            $columnWidth = max([$columnWidth, strlen($command->getName())]);

            $data[] = [
                $command->getName(),
                $this->translate($command->getShortDescription()),
            ];
        }

        return $this->renderSection($this->settings[self::SETTING_SHOW_TITLE_COMMANDS] && $showTitle, $this->translate('Commands') . ':' . PHP_EOL,
            $columnWidth, $data);
    }

    /**
     * @param Operand[] $operands
     *
     * @return string
     */
    private function renderOperands($operands) {
        $data        = [];
        $columnWidth = 0;
        foreach ($operands as $operand) {
            $definition = $this->surround($operand->getName(), self::TEXT_SURROUND_PLACEHOLDER);
            if (!$operand->isRequired()) {
                $definition = $this->surround($definition, self::TEXT_SURROUND_OPTIONAL);
            }

            $columnWidth = max([$columnWidth, strlen($definition)]);

            $data[] = [
                $definition,
                $this->translate($operand->getDescription()),
            ];
        }

        return $this->renderSection($this->settings[self::SETTING_SHOW_TITLE_OPERANDS], $this->translate('Operands') . ':' . PHP_EOL, $columnWidth, $data);
    }

    /**
     * @param Option[] $options
     *
     * @return string
     */
    private function renderOptions($options) {
        $data        = [];
        $columnWidth = 0;
        foreach ($options as $option) {
            $definition = implode(', ', array_filter([
                $option->getShort() ? '-' . $option->getShort() : null,
                $option->getLong() ? '--' . $option->getLong() : null,
            ]));
            if (!$option->getShort()) {
                $definition = '    ' . $definition;
            }

            if ($option->getMode() !== GetOpt::NO_ARGUMENT) {
                $argument = $this->surround($option->getArgument()->getName(), self::TEXT_SURROUND_PLACEHOLDER);
                if ($option->getMode() === GetOpt::OPTIONAL_ARGUMENT) {
                    $argument = $this->surround($argument, self::TEXT_SURROUND_OPTIONAL);
                }

                $definition .= ' ' . $argument;
            }

            $columnWidth = max([$columnWidth, strlen($definition)]);

            $data[] = [
                $definition,
                $this->translate($option->getDescription()),
            ];
        }

        return $this->renderSection($this->settings[self::SETTING_SHOW_TITLE_OPTIONS], $this->translate('Options') . ':' . PHP_EOL, $columnWidth, $data);
    }

    /**
     * @return string
     */
    private function renderUsage() {
        $text    = $this->translate('Usage') . ': ' . $this->getOpt->get(GetOpt::SETTING_SCRIPT_NAME) . ' ';
        $command = $this->getOpt->getCommand();
        // command
        if ($command) {
            $text .= $command->getName() . ' ';
        } elseif ($this->getOpt->hasCommands()) {
            $text .= $this->surround($this->translate('command'), self::TEXT_SURROUND_PLACEHOLDER) . ' ';
        }
        // options
        if ($this->getOpt->hasOptions() || !$this->getOpt->get(GetOpt::SETTING_STRICT_OPTIONS)) {
            $text .= $this->surround($this->translate('options'), self::TEXT_SURROUND_OPTIONAL) . ' ';
        }
        // operands
        if ($this->getOpt->hasOperands()) {
            $lastOperandMultiple = false;
            foreach ($this->getOpt->getOperandObjects() as $operand) {
                $name = $this->surround($operand->getName(), self::TEXT_SURROUND_PLACEHOLDER);
                if (!$operand->isRequired()) {
                    $name = $this->surround($name, self::TEXT_SURROUND_OPTIONAL);
                }
                $text .= $name . ' ';
                if ($operand->isMultiple()) {
                    $text .= $this->surround($this->surround($operand->getName(), self::TEXT_SURROUND_PLACEHOLDER) . self::TEXT_MULTIPLE,
                        self::TEXT_SURROUND_OPTIONAL);

                    $lastOperandMultiple = true;
                }
            }
            if (!$lastOperandMultiple && !$this->getOpt->get(GetOpt::SETTING_STRICT_OPERANDS)) {
                $text .= $this->surround($this->translate('operands'), self::TEXT_SURROUND_OPTIONAL);
            }
        }
        if ($this->settings[self::SETTING_SHOW_USAGE_DESCRIPTION]) {
            if ($command) {
                $text .= PHP_EOL . PHP_EOL . $this->translate($command->getDescription());
            }
        }

        return $text . PHP_EOL . PHP_EOL;
    }

    /**
     * Try to get console with.
     *
     * @return int
     */
    private function getScreenWidth() {
        if (!isset($this->settings[self::SETTING_SCREEN_WIDTH])) {
            $columns = defined('COLUMNS') ? (int) COLUMNS : (int) @getenv('COLUMNS');
            if (empty($columns)) {
                $process = proc_open('tput cols', [
                    1 => ['pipe', 'w'],
                    2 => ['pipe', 'w'],
                ], $pipes);
                $columns = (int) stream_get_contents($pipes[1]);
                proc_close($process);
            }

            if (empty($columns) && class_exists('\\Symfony\\Component\\Console\\Terminal')) {
                $terminal = new \Symfony\Component\Console\Terminal();
                $columns  = $terminal->getWidth();
            }

            $columns = empty($columns) ? self::SETTING_SCREEN_MAX_WIDTH : $columns;

            $this->settings[self::SETTING_SCREEN_WIDTH] = $columns;
        }

        return $this->settings[self::SETTING_SCREEN_WIDTH];
    }

    /**
     * Render section with optional title.
     *
     * @param $showTitle
     * @param $title
     * @param $columnWidth
     * @param $data
     *
     * @return string
     */
    private function renderSection($showTitle, $title, $columnWidth, $data) {
        if (!$showTitle) {
            $title = '';
        }

        return $title . $this->renderColumns($columnWidth, $data) . PHP_EOL;
    }

    /**
     * Surround text with symbols.
     *
     * @param $text
     * @param $with
     *
     * @return string
     */
    private function surround($text, $with) {
        return $with[0] . $text . substr($with, -1);
    }

    /**
     * Translator method
     *
     * @param string $string
     *
     * @return mixed
     */
    private function translate(string $string) {
        $translator = $this->translator;

        return $translator($string);
    }

}
