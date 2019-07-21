<?php

namespace Oforge\Engine\Modules\Console\Lib;

use GetOpt\GetOpt;

/**
 * Class Input
 * Stores input (options & operands) of GetOpt.
 *
 * @package Oforge\Engine\Modules\Console\Lib\GetOpt
 */
class Input {
    /**
     * @var array $operands
     */
    private $operands;
    /**
     * @var array $options
     */
    private $options;

    /**
     * Input constructor.
     *
     * @param GetOpt $getOpt
     */
    public function __construct(GetOpt $getOpt) {
        $operandValues = [];
        $index         = 0;
        foreach ($getOpt->getOperandObjects() as $operand) {
            $name  = $operand->getName();
            $value = $operand->getValue();

            $operandValues[$index++] = $value;
            if (isset($name) && $name !== 'arg') {
                $operandValues[$name] = $value;
            }
        }

        $this->operands = $operandValues;
        $this->options  = $getOpt->getOptions();
    }

    /**
     * Is option by $name set.
     *
     * @param string $name Short or long name of the option
     *
     * @return bool
     */
    public function hasOption($name) {
        return isset($this->options[$name]);
    }

    /**
     * Get an option by $name or null.
     *
     * @param string $name Short or long name of the option
     *
     * @return mixed|null
     */
    public function getOption($name) {
        return $this->options[$name] ?? null;
    }

    /**
     * Returns the list of options with a value.
     *
     * @return array
     */
    public function getOptions() {
        return $this->options;
    }

    /**
     * Is operand by $index set.
     *
     * @param int|string $index
     *
     * @return bool
     */
    public function hasOperand($index) {
        return isset($this->operands[$index]);
    }

    /**
     * Returns the nth operand (starting with 0), or null if it does not exist.
     * When $index is a string it returns the current value or the default value for the named operand.
     *
     * @param int|string $index
     *
     * @return mixed
     */
    public function getOperand($index) {
        return $this->operands[$index] ?? null;
    }

    /**
     * Returns the list of operands. Must be invoked after parse().
     *
     * @return array
     */
    public function getOperands() {
        return $this->operands;
    }

}
