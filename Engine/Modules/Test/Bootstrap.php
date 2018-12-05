<?php 

namespace Oforge\Engine\Modules\Test;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->endpoints = [
            "/test" => ["controller" => Controller\Frontend\HomeController::class, "name" => "homeTest"],
            "/backend" => ["controller" => Controller\Backend\Dashboard\Special\TestController::class, "name" => "homeTest"]
        ];
    }
	
	public function install() {
		// TODO: Implement install() method.
	}
	
	public function update() {
		// TODO: Implement update() method.
	}
	
	public function uninstall() {
		// TODO: Implement uninstall() method.
	}
	
	public function activate() {
		// TODO: Implement activate() method.
	}
	
	public function deactivate() {
		// TODO: Implement deactivate() method.
	}
}
