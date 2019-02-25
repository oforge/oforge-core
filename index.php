<?php
// session_start();
/**
 * This is the oforge portal starting point.
 */
define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/vendor/autoload.php';

$smith = BlackSmith::getInstance();
$smith->forge();
