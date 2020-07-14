<?php

namespace CookieConsent;

use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package CookieConsent
 */
class Bootstrap extends AbstractBootstrap {

    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->dependencies = [
            \CMS\Bootstrap::class,
        ];
    }

    /** @inheritDoc */
    public function activate() {
        I18N::translate('allow_cookies', [
            'en' => 'Accept',
            'de' => 'Zustimmen',
        ]);
        I18N::translate('decline_cookies', [
            'en' => 'Decline',
            'de' => 'Ablehnen',
        ]);
    }

}
