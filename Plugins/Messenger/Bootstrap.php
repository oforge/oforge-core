<?php

namespace Messenger;

use Messenger\Controller\Frontend\MessengerController;
use Messenger\Models\Conversation;
use Messenger\Models\Message;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;

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
    }

}
