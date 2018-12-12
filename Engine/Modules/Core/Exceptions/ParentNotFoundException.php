<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 04.12.2018
 * Time: 14:24
 */

namespace Oforge\Engine\Modules\Core\Exceptions;

class ParentNotFoundException extends \Exception
{
    /**
     * ConfigElementNotFoundException constructor.
     * @param $name
     */
    public function __construct(string $name)
    {
        parent::__construct("Parent element with name $name not found");
    }
}
