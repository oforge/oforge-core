<?php
namespace Themes\TemplateTest;

use Oforge\Engine\Modules\TemplateEngine\Abstracts\AbstractTemplate;

class Template extends AbstractTemplate {
    public function __construct() {
        parent::__construct();
        $this->parent = "Base";
        $this->addTemplateVariables([
            [
                'name' => 'primary',
                'value' => 'violet',
                'type' => 'color'
            ],
            [
                'name' => 'blub',
                'value' => 'grey',
                'type' => 'color'
            ],
            [
                'name'  => 'fourth',
                'value' => 'red',
                'type'  => 'color',
            ],
            [
                'name'  => 'third',
                'value' => '#fefe',
                'type'  => 'color',
            ],
        ]);
    }
}
