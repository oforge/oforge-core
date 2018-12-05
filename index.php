<?php
/**
 * This is the oforge portal starting point.
 */
define('ROOT_PATH', __DIR__);

require_once __DIR__.'/vendor/autoload.php';

$smith = BlackSmith::getInstance();
$smith->forge();
