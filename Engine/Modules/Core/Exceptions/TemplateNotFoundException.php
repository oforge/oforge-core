<?php

namespace Oforge\Engine\Modules\Core\Exceptions;

class TemplateNotFoundException extends \Exception
{
    /**
     * TemplateNotFoundException constructor.
     * @param $name
     */
    public function __construct($name) {
        parent::__construct("Template $name not found.");
    }
}
