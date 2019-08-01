<?php

namespace Messenger\Twig;


use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Twig\TwigFilter;
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
    public function getFilters() {
        return [
            new TwigFilter('single_digit',[$this, 'singleDigit']),
        ];
    }

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
    public function hasMessages($userId) : bool {
        /** @var FrontendMessengerService $messengerService */
        $messengerService = Oforge()->Services()->get('frontend.messenger');
        return $messengerService->hasUnreadMessages($userId);
    }

    /**
     * @param $number
     *
     * @return string
     */
    public function singleDigit($number) : string {
        if((int)$number < 10) {
            return (string)$number;
        }
        else {
            return '9+';
        }
    }
}
