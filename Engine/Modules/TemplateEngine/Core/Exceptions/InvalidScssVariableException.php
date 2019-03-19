<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 28.01.2019
 * Time: 10:36
 */

namespace Oforge\Engine\Modules\TemplateEngine\Core\Exceptions;

class InvalidScssVariableException extends \Exception {
    public function __construct($missingOption, $invalidVariables = null) {
        parent::__construct("Invalid variable. Missing option $missingOption. " . implode(", ", $invalidVariables));
    }
}