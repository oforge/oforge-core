<?php

namespace Oforge\Engine\Modules\Core\Helper;

use LimitIterator;
use SplFileObject;

/**
 * Class CsvHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class CsvHelper {
    public const DEFAULT_CONFIG = [
        'delimiter'           => ';',
        'enclosure'           => "\"",
        'escape'              => "\\",
        'skip-empty-line'     => true,
        'drop-newline-on-end' => true,
        'header-row'          => true,
    ];

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Read line by line the CSV. The columns are passed as array to the callable.
     *
     * @param string $filepath
     * @param callable $rowCallable
     * @param array $options
     *
     * @throws \Exception If the file is not readable.
     */
    public static function read(string $filepath, callable $rowCallable, array $options = []) {
        if (!is_readable($filepath)) {
            throw new \Exception("File '$filepath' is not readable.");
        }
        $options   = array_merge(self::DEFAULT_CONFIG, $options);
        $startLine = $options['header-row'] ? 1 : 0;
        $flags     = SplFileObject::READ_CSV;
        if ($options['skip-empty-line']) {
            $flags |= SplFileObject::SKIP_EMPTY;
        }
        if ($options['drop-newline-on-end']) {
            $flags |= SplFileObject::DROP_NEW_LINE;
        }

        $splFileObject = new SplFileObject($filepath);
        $splFileObject->setCsvControl($options['delimiter'], $options['enclosure'], $options['escape']);
        $splFileObject->setFlags($flags);
        $splFileObjectIterator = new LimitIterator($splFileObject, $startLine);
        foreach ($splFileObjectIterator as $csvRow) {
            $rowCallable($csvRow);
        }
    }

    /**
     * Writes CSV file if not exist.
     *
     * @param string $filepath
     * @param array $rows Rows array of column values.
     * @param array|null $header
     * @param array $options
     *
     * @throws \Exception If file already exist.
     */
    public static function write(string $filepath, array $rows, ?array $header = null, array $options = []) {
        if (file_exists($filepath)) {
            throw new \Exception("File '$filepath' already exist.");
        }
        $options   = array_merge(self::DEFAULT_CONFIG, $options);
        $delimiter = $options['delimiter'];
        $enclosure = $options['enclosure'];

        $enclosureCallable = function ($value) use ($enclosure) {
            return $enclosure . $value . $enclosure;
        };
        $joinRowCallable   = function ($rowValues) use ($enclosureCallable, $delimiter) {
            return implode($delimiter, array_map($enclosureCallable, $rowValues));
        };

        if (isset($header)) {
            $header = $joinRowCallable($header);
        }
        $header = isset($header) ? $header : '';
        file_put_contents($filepath, $header . "\n");
        foreach ($rows as $row) {
            file_put_contents($filepath, $joinRowCallable($row) . "\n", FILE_APPEND);
        }
    }

}
