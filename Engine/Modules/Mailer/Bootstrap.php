<?php

namespace Oforge\Engine\Modules\Mailer;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use SystemMailService\SystemMailService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Mailer
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->services = [
            'mail' => MailService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     */
    public function install() {
        // TODO in import csv
        // I18N::translate('config_mailer_host', 'Server', 'en');
        // I18N::translate('config_mailer_port', 'Server Port', 'en');
        // I18N::translate('config_mailer_username', 'Username', 'en');
        // I18N::translate('config_mailer_exceptions', 'Exceptions', 'en');
        // I18N::translate('config_mailer_smtp_password', 'Password', 'en');
        // I18N::translate('config_mailer_smtp_debug', 'Debug', 'en');
        // I18N::translate('config_mailer_smtp_auth', 'Auth', 'en');
        // I18N::translate('config_mailer_smtp_secure', 'TLS encryption', 'en');
        // I18N::translate('config_mailer_from', 'From', 'en');
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'     => 'mailer_host',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => '',
            'label'    => 'config_mailer_host',
            'required' => true,
            'order'    => 0,
        ]);
        $configService->add([
            'name'     => 'mailer_port',
            'type'     => ConfigType::INTEGER,
            'group'    => 'mailer',
            'default'  => 25,
            'label'    => 'config_mailer_port',
            'required' => true,
            'order'    => 1,
        ]);
        $configService->add([
            'name'     => 'mailer_smtp_username',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => '',
            'label'    => 'config_mailer_smtp_username',
            'required' => true,
            'order'    => 2,
        ]);
        $configService->add([
            'name'     => 'mailer_smtp_password',
            'type'     => ConfigType::PASSWORD,
            'group'    => 'mailer',
            'default'  => '',
            'label'    => 'config_mailer_smtp_password',
            'required' => true,
            'order'    => 3,
        ]);
        $configService->add([
            'name'     => 'mailer_smtp_secure',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => 'ssl',
            'label'    => 'config_mailer_smtp_secure',
            'required' => true,
            'order'    => 4,
        ]);
        $configService->add([
            'name'     => 'mailer_smtp_auth',
            'type'     => ConfigType::BOOLEAN,
            'group'    => 'mailer',
            'default'  => true,
            'label'    => 'config_mailer_smtp_auth',
            'required' => true,
            'order'    => 5,
        ]);
        $configService->add([
            'name'     => 'mailer_smtp_debug',
            'type'     => ConfigType::INTEGER,
            'group'    => 'mailer',
            'default'  => 2,
            'label'    => 'config_mailer_smtp_debug',
            'required' => true,
            'order'    => 6,
        ]);
        $configService->add([
            'name'     => 'mailer_exceptions',
            'type'     => ConfigType::BOOLEAN,
            'group'    => 'mailer',
            'default'  => true,
            'label'    => 'config_mailer_exceptions',
            'required' => true,
            'order'    => 7,
        ]);
        $configService->add([
            'name'     => 'mailer_from_host',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => '',
            'label'    => 'config_mailer_from_host',
            'required' => true,
            'order'    => 8,
        ]);
        $configService->add([
            'name'     => 'mailer_from_info',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => 'info',
            'label'    => 'config_mailer_from_info',
            'required' => false,
            'order'    => 9,
        ]);
        $configService->add([
            'name'     => 'mailer_from_no_reply',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => 'no-reply',
            'label'    => 'config_mailer_from_no_reply',
            'required' => false,
            'order'    => 10,
        ]);
    }
}
