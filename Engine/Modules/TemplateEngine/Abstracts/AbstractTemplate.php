<?php

namespace Oforge\Engine\Modules\TemplateEngine\Abstracts;

use Oforge\Engine\Modules\TemplateEngine\Services\ScssVariableService;

abstract class AbstractTemplate {
    public $parent;
    protected $context;
    protected $templateVariables = [];

    /**
     * AbstractTemplate constructor
     */
    public function __construct() {
        $this->context = static::class;
    }

    /**
     * @param array $variables
     */
    protected function addTemplateVariables(array $variables) {
        $this->templateVariables = array_merge($variables, $this->templateVariables);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\TemplateEngine\Exceptions\InvalidScssVariableException
     */
    public function registerTemplateVariables() {
        /** @var ScssVariableService $scssVariables */
        $scssVariables = Oforge()->Services()->get('scss.variables');
        foreach ($this->templateVariables as $templateVariable) {
            $templateVariable['context'] = $this->context;
            $scssVariables->add($templateVariable);
        }
    }
}
