<?php

namespace Oforge\Engine\Modules\Core\Exceptions\Template;

use Exception;

/**
 * Class TemplateNotFoundException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions
 */
class TemplateNotFoundException extends Exception {

    /**
     * TemplateNotFoundException constructor.
     *
     * @param string $templateName
     */
    public function __construct(string $templateName) {
        parent::__construct("Template with name '$templateName' not found!");
    }

}
