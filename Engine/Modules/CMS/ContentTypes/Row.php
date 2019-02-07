<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 12:13
 */

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;

class Row extends AbstractContentType {
    function init() {
        $configuration = [
            "width" => "100px",
            "height" => "100px",
            "margin-top" => "0",
            "margin-left" => "0",
            "margin-bottom" => "0",
            "margin-right" => "0",
            "padding-top" => "0",
            "padding-left" => "0",
            "padding-bottom" => "0",
            "padding-right" => "0",
            "border-style" => "1",
            "border-size" => "solid",
            "border-color" => "#000"
        ];
    }
    
    public function getContent() {
    }
    
    public function setContent($content) {
    }
    
    public function load(int $id) {
    }
    
    public function save(array $params) {
    }
}
