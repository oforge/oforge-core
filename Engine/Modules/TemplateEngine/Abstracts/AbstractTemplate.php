<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 08.11.2018
 * Time: 11:20
 */
namespace Oforge\Engine\Modules\TemplateEngine\Abstracts;

use mysql_xdevapi\Exception;
use Oforge\Engine\Modules\TemplateEngine\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Services\ScssVariableService;

abstract class AbstractTemplate {
    public $parent;
    private $context = self::class;
    private $templateVariables = [];

    public function registerTemplateVariables() {
        /**
         * @var ScssVariableService $scssVariables
         */
        $scssVariables = Oforge()->Services()->get('scss.variables');
        if ($this->isValid($this->templateVariables)) {

            foreach ($this->templateVariables as $templateVariable)

            $scssVariables->add(
                $templateVariable['name'],
                $templateVariable["value"],
                $templateVariable["type"],
                $templateVariable["context"]
                );
        }
    }


}