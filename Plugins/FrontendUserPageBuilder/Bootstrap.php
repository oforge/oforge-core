<?php

namespace FrontendUserPageBuilder;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use FrontendUserPageBuilder\Controller\FrontendUserPageBuilderController;
use FrontendUserPageBuilder\Services\FrontendUserPageBuilderService;
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
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\AccessExtension;

/**
 * Class Bootstrap
 *
 * @package FrontendUserPageBuilder
 */
class Bootstrap extends AbstractBootstrap {

    public function __construct() {
        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class,
            \Insertion\Bootstrap::class
        ];

        $this->endpoints = [
            FrontendUserPageBuilderController::class,
        ];

        $this->services = [
            'frontend.user.pagebuilder' => FrontendUserPageBuilderService::class,
        ];

        $this->order = 9999;
    }

    public function install() {
    }

    public function activate() {

    }
}
