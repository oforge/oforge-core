<?php
namespace Themes\TemplateTest;

use Oforge\Engine\Modules\TemplateEngine\Abstracts\AbstractTemplate;

class Template extends AbstractTemplate {
    public function __construct() {
        $this->parent = "Base";
    }
}
