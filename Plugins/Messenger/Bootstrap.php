<?php

namespace Messenger;

use Messenger\Controller\Frontend\MessengerController;
use Messenger\Models\Conversation;
use Messenger\Models\Message;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
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
}
