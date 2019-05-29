<?php

namespace Messenger;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Services\AccountNavigationService;
use Messenger\Controller\Frontend\MessengerController;
use Messenger\Models\Conversation;
use Messenger\Models\Message;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

/**
 * Class Bootstrap
 *
 * @package Messenger
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            MessengerController::class,
        ];

        $this->models = [
            Conversation::class,
            Message::class,
        ];

        $this->services = [
            'frontend.messenger' => FrontendMessengerService::class,
        ];

        $this->dependencies = [
            \FrontendUserManagement\Bootstrap::class
        ];
    }

    /**
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            'name'     => 'frontend_account_messages',
            'order'    => 1,
            'icon'     => 'postfach',
            'path'     => 'frontend_account_messages',
            'position' => 'sidebar',
        ]);
    }

}
