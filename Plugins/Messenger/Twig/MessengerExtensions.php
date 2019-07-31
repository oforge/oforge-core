<?php

namespace Messenger\Twig;


use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Twig_Extension;
use Twig_ExtensionInterface;

/**
 * Class AccessExtension
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Twig
 */
class MessengerExtensions extends Twig_Extension implements Twig_ExtensionInterface {
    /**
     * @inheritDoc
     */
    public function getFunctions() {
        return [
            new \Twig_Function('has_messages', [$this, 'hasMessages']),
        ];
    }

    /**
     * @param $userId
     *
     * @return bool
     * @throws ServiceNotFoundException
     */
    public function hasMessages($userId) {
        /** @var FrontendMessengerService $messengerService */
        $messengerService = Oforge()->Services()->get('frontend.messenger');
        return $messengerService->hasUnreadMessages($userId);
    }
}
