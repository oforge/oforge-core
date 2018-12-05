<?php 

namespace Oforge\Engine\Modules\Mailer;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->services = [
            "mail" => \Oforge\Engine\Modules\Mailer\Services\MailService::class
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