<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 23.11.2018
 * Time: 09:44
 */

namespace Oforge\Engine\Modules\Core\Exceptions;

class PluginNotFoundException extends \Exception
{
    /**
     * CouldNotInstallPluginException constructor.
     * @param string $classname
     */
    public function __construct($classname)
    {
        parent::__construct("The plugin $classname could not be found.");
    }
}