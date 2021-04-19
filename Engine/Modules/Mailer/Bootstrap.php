<?php

namespace Oforge\Engine\Modules\Mailer;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Mailer
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
        // $this->models = [
        //     Models\Mail::class,
        // ];

        $this->services = [
            'mail'           => Services\MailService::class,
            // 'mail.list'      => Services\MailingListService::class,
            'mail.inlineCss' => Services\InlineCssService::class,
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     */
    public function install()
    {
        I18N::translate(
            'config_mailer_smtp_host',
            [
                'en' => 'SMTP host',
                'de' => 'SMTP Host',
            ]
        );
        I18N::translate(
            'config_mailer_smtp_port',
            [
                'en' => 'SMTP port',
                'de' => 'SMTP Port',
            ]
        );
        I18N::translate(
            'config_mailer_smtp_username',
            [
                'en' => 'SMTP username',
                'de' => 'SMTP Benutzername',
            ]
        );
        I18N::translate(
            'config_mailer_smtp_password',
            [
                'en' => 'SMTP password',
                'de' => 'SMTP Passwort',
            ]
        );
        I18N::translate(
            'config_mailer_smtp_auth',
            [
                'en' => 'Activate SMTP authentication',
                'de' => 'Aktiviere SMTP Authentifizierung',
            ]
        );
        I18N::translate(
            'config_mailer_smtp_secure',
            [
                'en' => 'SMTP encryption (tls or ssl)',
                'de' => 'SMTP Verschlüsselung (tls oder ssl)',
            ]
        );
        I18N::translate(
            'config_mailer_smtp_debug',
            [
                'en' => 'SMTP Debug Level (0 - 4)',
                'de' => 'SMTP Debug Level (0 - 4)',
            ]
        );
        I18N::translate(
            'config_mailer_throw_exceptions',
            [
                'en' => 'Using exceptions',
                'de' => 'Verwenden von Exceptions',
            ]
        );
        I18N::translate(
            'config_mailer_from_builder_name',
            [
                'en' => 'From builder: Name',
                'de' => 'Absender-Builder: Name',
            ]
        );
        I18N::translate(
            'config_mailer_from_builder_mail_host',
            [
                'en' => 'From builder: Mail host',
                'de' => 'Absender-Builder: Mail-Host',
            ]
        );
        I18N::translate(
            'config_mailer_from_builder_mail_prefix_info',
            [
                'en' => 'From builder: Mail prefix for info mails',
                'de' => 'Absender-Builder: Mail-Prefix für Info-Mails',
            ]
        );
        I18N::translate(
            'config_mailer_from_builder_mail_prefix_no_reply',
            [
                'en' => 'From builder: Mail-Prefix for no-reply mails',
                'de' => 'Absender-Builder: Mail-Prefix für No-Reply Mails',
            ]
        );
        I18N::translate(
            'config_mailer_dev_redirect_enabled',
            [
                'en' => 'Dev: Redirect outgoing mail',
                'de' => 'Dev: Ausgehende Mails umleiten',
            ]
        );
        I18N::translate(
            'config_mailer_dev_redirect_to',
            [
                'en' => 'Dev: Recipient for mail redirects',
                'de' => 'Dev: Empfänger für Mail-Umleitungen',
            ]
        );
        I18N::translate(
            'config_mailer_dev_redirect_to',
            [
                'en' => 'recipient for outgoing mail',
                'de' => 'Dev: Empfänger für Mail-Umleitungen',
            ]
        );

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configService->add(
            [
                'name'     => 'mailer_dev_redirect_enabled',
                'type'     => ConfigType::BOOLEAN,
                'group'    => 'mailer',
                'default'  => false,
                'label'    => 'config_mailer_dev_redirect_enabled',
                'required' => false,
                'order'    => 0,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_dev_redirect_to',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => '',
                'label'    => 'config_mailer_dev_redirect_to',
                'required' => false,
                'order'    => 1,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_smtp_host',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => '',
                'label'    => 'config_mailer_smtp_host',
                'required' => true,
                'order'    => 2,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_smtp_port',
                'type'     => ConfigType::INTEGER,
                'group'    => 'mailer',
                'default'  => 25,
                'label'    => 'config_mailer_smtp_port',
                'required' => true,
                'order'    => 3,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_smtp_username',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => '',
                'label'    => 'config_mailer_smtp_username',
                'required' => true,
                'order'    => 4,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_smtp_password',
                'type'     => ConfigType::STRING,#ConfigType::PASSWORD,# (sometimes) encryption problems
                'group'    => 'mailer',
                'default'  => '',
                'label'    => 'config_mailer_smtp_password',
                'required' => true,
                'order'    => 5,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_smtp_secure',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => 'tls',
                'label'    => 'config_mailer_smtp_secure',
                'required' => true,
                'order'    => 6,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_smtp_auth',
                'type'     => ConfigType::BOOLEAN,
                'group'    => 'mailer',
                'default'  => true,
                'label'    => 'config_mailer_smtp_auth',
                'required' => false,
                'order'    => 7,
            ]
        );
        $configService->add(//TODO not used, usage?
            [
                'name'     => 'mailer_smtp_debug',
                'type'     => ConfigType::INTEGER,
                'group'    => 'mailer',
                'default'  => 2,
                'label'    => 'config_mailer_smtp_debug',
                'required' => true,
                'order'    => 8,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_throw_exceptions',
                'type'     => ConfigType::BOOLEAN,
                'group'    => 'mailer',
                'default'  => true,
                'label'    => 'config_mailer_throw_exceptions',
                'required' => false,
                'order'    => 8,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_from_builder_name',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => '',
                'label'    => 'config_mailer_from_builder_name',
                'required' => false,
                'order'    => 9,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_from_builder_mail_host',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => '',
                'label'    => 'config_mailer_from_builder_mail_host',
                'required' => true,
                'order'    => 10,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_from_builder_mail_prefix_info',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => 'info',
                'label'    => 'config_mailer_from_builder_mail_prefix_info',
                'required' => false,
                'order'    => 11,
            ]
        );
        $configService->add(
            [
                'name'     => 'mailer_from_builder_mail_prefix_no_reply',
                'type'     => ConfigType::STRING,
                'group'    => 'mailer',
                'default'  => 'no-reply',
                'label'    => 'config_mailer_from_builder_mail_prefix_no_reply',
                'required' => false,
                'order'    => 12,
            ]
        );
    }

}
