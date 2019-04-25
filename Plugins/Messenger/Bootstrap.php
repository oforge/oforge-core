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
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistsException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->services = [
            'frontend.messenger' => FrontendMessengerService::class,
        ];

        $this->models = [
            Conversation::class,
            Message::class,
        ];
        $this->endpoints = [
            '/account/messages[/{id:.*}]' => [
                'controller'  => MessengerController::class,
                'name'        => 'frontend_account_messages',
                'asset_scope' => 'Frontend',
            ],
        ];
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistsException
     * @throws ConfigOptionKeyNotExistsException
     * @throws ParentNotFoundException
     * @throws ServiceNotFoundException
     */
    public function install() {
        /** @var AccountNavigationService $accountNavigationService */
        $accountNavigationService = Oforge()->Services()->get('frontend.user.management.account.navigation');

        $accountNavigationService->put([
            "name" => "frontend_account_messages",
            "order" => 1,
            "icon" => "postfach",
            "path" => "frontend_account_messages",
            "position" => "sidebar",
        ]);
    }
}
