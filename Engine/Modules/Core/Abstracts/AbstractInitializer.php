<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 09.11.2018
 * Time: 10:55
 */

namespace Oforge\Engine\Modules\Core\Abstracts;

/**
 * Class AbstractInitializer
 * Used for initializing modules
 * @package Oforge\Engine\Modules\Core\Abstracts
 */
abstract class AbstractInitializer {
    public abstract function init();
}