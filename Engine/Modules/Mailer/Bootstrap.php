<?php 

namespace Oforge\Engine\Modules\Mailer;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Services\ConfigService;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->services = [
            "mail" => \Oforge\Engine\Modules\Mailer\Services\MailService::class
        ];

        $this->endpoints = [
            "/" => ["controller" => \Test\Controller\Frontend\MailController::class, "name" => "mail"]
        ];
    }


    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */



    public function install() {

        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get("config");

        $configService->update([
            "name" => "mailer.host",
            "label" => "E-Mail Server",
            "type" => "string",
            "required" => true,
            "default" => ""
        ]);
        $configService->update([
            "name" => "mailer.username",
            "label" => "E-Mail Username",
            "type" => "string",
            "required" => true,
            "default" => ""
        ]);
        $configService->update([
            "name" => "mailer.port",
            "label" => "E-Mail Server Port",
            "type" => "integer",
            "required" => true,
            "default" => 587
        ]);
        $configService->update([
            "name" => "mailer.exceptions",
            "label" => "E-Mail Exceptions",
            "type" => "boolean",
            "required" => true,
            "default" => true
        ]);
        $configService->update([
            "name" => "mailer.smtp.password",
            "label" => "SMTP Password",
            "type" => "string",
            "required" => true,
            "default" => ""
        ]);
        $configService->update([
            "name" => "mailer.smtp.debug",
            "label" => "STMP Debug",
            "type" => "integer",
            "required" => true,
            "default" => 2
        ]);
        $configService->update([
            "name" => "mailer.smtp.auth",
            "label" => "SMTP Auth",
            "type" => "boolean",
            "required" => true,
            "default" => true
        ]);
        $configService->update([
            "name" => "mailer.smtp.secure",
            "label" => "Enable TLS encryption",
            "type" => "string",
            "required" => true,
            "default" => ""
        ]);
        $configService->update([
            "name" => "mailer.from",
            "label" => "Mailer From",
            "type" => "string",
            "required" => true,
            "default" => ""
        ]);

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