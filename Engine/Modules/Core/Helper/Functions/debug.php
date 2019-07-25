<?php

/**
 * Echo or return data wrapped in script console.log.
 *
 * @param array $data
 * @param bool $echo
 *
 * @return string
 */
function o_dump(array $data, $echo = true) : string {
    $string = '<script>console.log(' . json_encode($data) . ')</script>';
    if ($echo) {
        echo $string;
    }

    return $string;
}

/**
 * Echo or return print_r data wrapped in pre tag.
 *
 * @param array $data
 * @param bool $echo
 *
 * @return string
 */
function o_print(array $data, $echo = true) : string {
    if (is_array($data)) {
        array_walk_recursive($data, function (&$item) {
            if ($item === null) {
                $item = 'null';
            } elseif (is_bool($item)) {
                $item = $item ? 'true' : 'false';
            }
        });
    }
    $string = '<pre>' . print_r($data, true) . '</pre>';
    if ($echo) {
        echo $string;
    }

    return $string;
}
