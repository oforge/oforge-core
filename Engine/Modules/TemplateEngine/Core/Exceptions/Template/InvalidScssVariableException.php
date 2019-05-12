<?php

namespace Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\Template;

/**
 * Class InvalidScssVariableException
 *
 * @package Oforge\Engine\Modules\Core\Exceptions\Template
 */
class InvalidScssVariableException extends \Exception {
    /** @var null */
    private $invalidVariables;

    public function __construct($missingOption, $invalidVariables = null) {
        parent::__construct("Invalid variable. Missing option $missingOption. " . implode(', ', $invalidVariables));
        $this->invalidVariables = $invalidVariables;
    }

    /**
     * @return null
     */
    public function getInvalidVariables() {
        return $this->invalidVariables;
    }

}
