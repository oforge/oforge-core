<?php

namespace Mailchimp;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use Mailchimp\Controller\Frontend\AccountNewsletterController;
use Mailchimp\Controller\Frontend\NewsletterSubscriptionController;
use Mailchimp\Services\MailchimpNewsletterService;
use Mailchimp\Models\UserNewsletter;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Models\Config\ConfigType;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Mailchimp
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
        ];

        $this->endpoints = [
            NewsletterSubscriptionController::class,
            AccountNewsletterController::class,
        ];

        $this->models = [
            UserNewsletter::class,
        ];

        $this->services = [
            'mailchimp.newsletter' => MailchimpNewsletterService::class,
        ];
    }

    /**
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function install() {
        //TODO in import csv
        // I18N::translate('config_mailchimp_uri', 'Mailchimp URI', 'en');
        // I18N::translate('config_mailchimp_username', 'Username', 'en');
        // I18N::translate('config_mailchimp_api_key', 'API Key', 'en');
        // I18N::translate('config_mailchimp_data_center', 'Data Center', 'en');
        // I18N::translate('config_mailchimp_list_id', 'List ID', 'en');

        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');

        $configService->add([
            'name'    => 'mailchimp_uri',
            'type'    => ConfigType::STRING,
            'group'   => 'mailchimp',
            'default' => 'https://{dc}.api.mailchimp.com/3.0',
            'label'   => 'config_mailchimp_uri',
            'order'   => 0,
        ]);
        $configService->add([
            'name'    => 'mailchimp_username',
            'type'    => ConfigType::STRING,
            'group'   => 'mailchimp',
            'default' => '',
            'label'   => 'config_mailchimp_username',
            'order'   => 1,
        ]);
        $configService->add([
            'name'     => 'mailchimp_api_key',
            'type'     => ConfigType::STRING,
            'group'    => 'mailchimp',
            'default'  => '',
            'label'    => 'config_mailchimp_api_key',
            'required' => true,
            'order'    => 2,
        ]);
        $configService->add([
            'name'     => 'mailchimp_data_center',
            'type'     => ConfigType::STRING,
            'group'    => 'mailchimp',
            'default'  => '',
            'label'    => 'config_mailchimp_data_center',
            'required' => true,
            'order'    => 3,
        ]);
        $configService->add([
            'name'     => 'mailchimp_list_id',
            'type'     => ConfigType::STRING,
            'group'    => 'mailchimp',
            'default'  => '',
            'label'    => 'config_mailchimp_list_id',
            'required' => true,
            'order'    => 4,
        ]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function activate() {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            'name'     => 'frontend_account_newsletter',
            'order'    => 1,
            'icon'     => 'mail_open',
            'path'     => 'frontend_account_newsletter',
            'position' => 'sidebar',
        ]);
    }
}
