<?php

namespace Mailchimp;

use Mailchimp\Controller\Frontend\NewsletterSubscriptionController;
use Mailchimp\Services\MailchimpNewsletterService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

/**
 * Class Bootstrap
 *
 * @package Mailchimp
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->endpoints = [
            NewsletterSubscriptionController::class,
        ];

        $this->services = [
            'mailchimp.newsletter' => MailchimpNewsletterService::class,
        ];
    }

}
