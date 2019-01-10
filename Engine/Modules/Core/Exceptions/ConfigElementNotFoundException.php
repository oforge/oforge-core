<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 04.12.2018
 * Time: 14:24
 */

namespace Oforge\Engine\Modules\Core\Exceptions;

class ConfigElementNotFoundException extends \Exception
{
    /**
     * ConfigElementNotFoundException constructor.
     * @param $name
     */
    public function __construct(string $name, ?string $scope)
    {
        parent::__construct("Config key $name not found for scope $scope");
    }
}
