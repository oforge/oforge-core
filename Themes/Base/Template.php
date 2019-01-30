<?php
namespace Themes\Base;

use Oforge\Engine\Modules\TemplateEngine\Abstracts\AbstractTemplate;

class Template extends AbstractTemplate {
    private $vars = [
        [
            'name' => '$primary',
            'value' => '#000',
            'type' => 'color',
        ],
        [
            'name' => '$secondary',
            'value' => '#fff',
            'type' => 'color',
        ],
    ];

    public function registerTemplateVariables() {

        // $scssVariables->put('$primary', "#000", "1", "color", "0", "Base");

    }
}

