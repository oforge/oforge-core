<?php

namespace Mailchimp;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Mailchimp\Controller\Frontend\NewsletterSubscriptionController;
use Mailchimp\Services\MailchimpNewsletterService;
use Oforge\Engine\Modules\Core\Services\ConfigService;

class Bootstrap extends AbstractBootstrap
{
    public function __construct()
    {
        $this->endpoints = [
            "/newsletter" => ["controller" => NewsletterSubscriptionController::class,
                "name" => "frontend_newsletter_subscription"]
        ];
        $this->services = [
            'mailchimp.newsletter' => MailchimpNewsletterService::class,
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
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
            "group" => "mailchimp"
        ]);
        $configService->update([
            "name" => "mailchimp_username",
            "label" => "Username",
            "type" => "string",
            "required" => false,
            "default" => "",
            "group" => "mailchimp"
        ]);
        $configService->update([
            "name" => "mailchimp_api_key",
            "label" => "API Key",
            "type" => "string",
            "required" => true,
            "default" => "",
            "group" => "mailchimp"
        ]);

        $configService->update([
            "name" => "mailchimp_data_center",
            "label" => "Mailchimp Data Center",
            "type" => "string",
            "required" => true,
            "default" => "",
            "group" => "mailchimp"
        ]);
        $configService->update([
            "name" => "mailchimp_list_id",
            "label" => "List ID",
            "type" => "string",
            "required" => true,
            "default" => "",
            "group" => "mailchimp"
        ]);
    }
}