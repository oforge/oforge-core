<?php

namespace Mailchimp;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use Mailchimp\Controller\Frontend\AccountNewsletterController;
use Mailchimp\Controller\Frontend\NewsletterSubscriptionController;
use Mailchimp\Services\MailchimpNewsletterService;
use Mailchimp\Views\Plugins\Mailchimp\Models\UserNewsletter;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;

/**
 * Class Bootstrap
 *
 * @package Mailchimp
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
        $this->endpoints = [
            NewsletterSubscriptionController::class,
            AccountNewsletterController::class,
        ];

        $this->services = [
            'mailchimp.newsletter' => MailchimpNewsletterService::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
        ];

        $this->models = [
            UserNewsletter::class,
        ];
    }

    /**
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function install()
    {

        /**
         * @var $configService ConfigService
         */
        $configService = Oforge()->Services()->get("config");

        $configService->update([
            "name" => "mailchimp_uri",
            "label" => "Mailchimp URI",
            "type" => "string",
            "required" => false,
            "default" => "https://{dc}.api.mailchimp.com/3.0",
            "group" => "Mailchimp"
        ]);
        $configService->update([
            "name" => "mailchimp_username",
            "label" => "Username",
            "type" => "string",
            "required" => false,
            "default" => "",
            "group" => "Mailchimp"
        ]);
        $configService->update([
            "name" => "mailchimp_api_key",
            "label" => "API Key",
            "type" => "string",
            "required" => true,
            "default" => "",
            "group" => "Mailchimp"
        ]);

        $configService->update([
            "name" => "mailchimp_data_center",
            "label" => "Mailchimp Data Center",
            "type" => "string",
            "required" => true,
            "default" => "",
            "group" => "Mailchimp"
        ]);
        $configService->update([
            "name" => "mailchimp_list_id",
            "label" => "List ID",
            "type" => "string",
            "required" => true,
            "default" => "",
            "group" => "Mailchimp"
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
    public function activate()
    {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            'name' => 'frontend_account_newsletter',
            'order' => 1,
            'icon' => 'postfach',
            'path' => 'frontend_account_newsletter',
            'position' => 'sidebar',
        ]);
    }
}
