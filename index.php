<?php
// session_start();
/**
 * This is the oforge portal starting point.
 */
define('OFORGE_SCRIPT_TIMEOUT', 90);
define('ROOT_PATH', __DIR__);

if (((int) ini_get('max_execution_time')) < OFORGE_SCRIPT_TIMEOUT) {
    set_time_limit(OFORGE_SCRIPT_TIMEOUT);
}

require_once ROOT_PATH . '/vendor/autoload.php';

$smith = BlackSmith::getInstance();
$smith->forge();
