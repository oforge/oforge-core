<?php

namespace Oforge\Engine\Modules\Mailer;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Mailer\Services\MailService;

class Bootstrap extends AbstractBootstrap {
    public function __construct() {
        $this->services = [
            "mail" => MailService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get("config");

        $configService->update([
            "name"     => "mailer_host",
            "label"    => "E-Mail Server",
            "type"     => "string",
            "required" => true,
            "default"  => "",
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_username",
            "label"    => "E-Mail Username",
            "type"     => "string",
            "required" => true,
            "default"  => "",
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_port",
            "label"    => "E-Mail Server Port",
            "type"     => "integer",
            "required" => true,
            "default"  => 587,
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_exceptions",
            "label"    => "E-Mail Exceptions",
            "type"     => "boolean",
            "required" => true,
            "default"  => true,
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_smtp_password",
            "label"    => "SMTP Password",
            "type"     => "string",
            "required" => true,
            "default"  => "",
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_smtp_debug",
            "label"    => "STMP Debug",
            "type"     => "integer",
            "required" => true,
            "default"  => 2,
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_smtp_auth",
            "label"    => "SMTP Auth",
            "type"     => "boolean",
            "required" => true,
            "default"  => true,
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_smtp_secure",
            "label"    => "Enable TLS encryption",
            "type"     => "string",
            "required" => true,
            "default"  => "",
            "group"    => "mailer",
        ]);
        $configService->update([
            "name"     => "mailer_from",
            "label"    => "Mailer From",
            "type"     => "string",
            "required" => true,
            "default"  => "",
            "group"    => "mailer",
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
