<?php
namespace Themes\Base;

use Oforge\Engine\Modules\TemplateEngine\Abstracts\AbstractTemplate;
use Oforge\Engine\Modules\TemplateEngine\Models\ScssVariable;
use Oforge\Engine\Modules\TemplateEngine\Services\ScssVariableService;

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

