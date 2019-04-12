<?php
// session_start();
/**
 * This is the oforge portal starting point.
 */
define('ROOT_PATH', __DIR__);

if (((int) ini_get('max_execution_time')) < 90) {
    set_time_limit(90);
}

require_once ROOT_PATH . '/vendor/autoload.php';

$smith = BlackSmith::getInstance();
$smith->forge();
