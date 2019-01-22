<?php

namespace Oforge\Engine\Modules\Console\Lib\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;

/**
 * Class ConsoleFormatter
 *
 * @package Oforge\Engine\Modules\Console\Lib\Monolog\Formatter
 */
class ConsoleFormatter extends LineFormatter {

    /**
     * ConsoleFormatter constructor.
     *
     * @param string $format
     */
    public function __construct(string $format) {
        parent::__construct($format);
        $this->ignoreEmptyContextAndExtra();
        $this->allowInlineLineBreaks();
        $this->includeStacktraces();
    }

    /**
     * @param string $format
     */
    public function setFormat(string $format) : void {
        $this->format = $format;
    }

}
