<?php

namespace Oforge\Engine\Modules\Mailer;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Mailer\Services\InlineCssService;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Oforge\Engine\Modules\Mailer\Services\MailingListService;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Mailer
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->services = [
            'mail'         => MailService::class,
            'mailing.list' => MailingListService::class,
            'inline.css'   => InlineCssService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     */
    public function install() {
        I18N::translate('config_mailer_host',[
          'en' => 'Mailer Host',
          'de' => 'Mailer Host',
        ]);
        I18N::translate('config_mailer_port',[
            'en' => 'Mailer Port',
            'de' => 'Mailer Port',
        ]);
        I18N::translate('config_mailer_smtp_username',[
            'en' => 'SMTP Username',
            'de' => 'SMTP Benutzername',
        ]);
        I18N::translate('config_mailer_smtp_password',[
            'en' => 'SMTP Password',
            'de' => 'SMTP Passwort',
        ]);
        I18N::translate('config_mailer_smtp_secure',[
            'en' => 'Activate SMTP Authentication',
            'de' => 'Aktiviere SMTP Authentifizierung',
        ]);
        I18N::translate('config_mailer_smtp_auth',[
            'en' => 'SMTP Encription (tls or ssl)',
            'de' => 'SMTP Verschlüsselung (tls oder ssl)',
        ]);
        I18N::translate('mailer_smtp_debug',[
            'en' => 'SMTP Debug Level (0 - 4)',
            'de' => 'SMTP Debug Level (0 - 4)',
        ]);
        I18N::translate('config_mailer_exceptions',[
            'en' => 'Mailer Exceptions',
            'de' => 'Mailer Exceptions',
        ]);
        I18N::translate('config_mailer_from_name',[
            'en' => 'Sender Name',
            'de' => 'Absendername',
        ]);
        I18N::translate('config_mailer_from_host',[
            'en' => 'Sender Hostname',
            'de' => 'Absender Hostname',
        ]);
        I18N::translate('config_mailer_from_host',[
            'en' => 'Sender Hostname',
            'de' => 'Absender Hostname',
        ]);
        I18N::translate('config_mailer_from_info',[
            'en' => 'Sender Prefix for Info Mails',
            'de' => 'Absender Präfix für Info-Mails',
        ]);
        I18N::translate('config_mailer_from_no_reply',[
            'en' => 'Sender Prefix for No-Reply Mails',
            'de' => 'Absender Präfix für No-Reply Mails',
        ]);

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
            'type'     => ConfigType::STRING,
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
            'name'     => 'mailer_from_name',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => '',
            'label'    => 'config_mailer_from_name',
            'required' => false,
            'order'    => 8
        ]);
        $configService->add([
            'name'     => 'mailer_from_host',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => '',
            'label'    => 'config_mailer_from_host',
            'required' => true,
            'order'    => 9,
        ]);
        $configService->add([
            'name'     => 'mailer_from_info',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => 'info',
            'label'    => 'config_mailer_from_info',
            'required' => false,
            'order'    => 10,
        ]);
        $configService->add([
            'name'     => 'mailer_from_no_reply',
            'type'     => ConfigType::STRING,
            'group'    => 'mailer',
            'default'  => 'no-reply',
            'label'    => 'config_mailer_from_no_reply',
            'required' => false,
            'order'    => 11,
        ]);
    }
}
