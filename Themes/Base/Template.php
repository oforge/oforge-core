<?php
namespace Themes\Base;

use Oforge\Engine\Modules\TemplateEngine\Core\Abstracts\AbstractTemplate;

class Template extends AbstractTemplate {

    public function __construct() {
        parent::__construct();
        $this->addTemplateVariables(
            [
                [
                    'name' => 'primary',
                    'value' => '#000',
                    'type' => 'color',
                ],
                [
                    'name' => 'secondary',
                    'value' => '#fff',
                    'type' => 'color',
                ],
            ]
        );
    }
}
