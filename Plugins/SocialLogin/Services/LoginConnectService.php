<?php

namespace SocialLogin\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;
use SocialLogin\Models\LoginProvider;
use Hybridauth\Hybridauth;

/**
 * Class LoginProviderService
 *
 * @package Seo\Services
 */
class LoginConnectService {

    private $profiles = [];

    public function connect(string $name, string $callback) : bool {
        /**
         * @var $service LoginProviderService
         */
        $service  = Oforge()->Services()->get("sociallogin.providers");
        $provider = $service->getByName($name);

        if ($provider != null) {
            $configElement = ['enabled' => true, 'keys' => []];

            if ($provider->getAppId() != null) {
                $configElement['keys']['id'] = $provider->getAppId();
            }

            if ($provider->getSecret() != null) {
                $configElement['keys']['secret'] = $provider->getSecret();
            }

            if ($provider->getAppKey() != null) {
                $configElement['keys']['key'] = $provider->getAppKey();
            }

            $config = [
                'callback'  => $callback,
                'providers' => [
                    $provider->getName() => $configElement,
                ],
            ];

            $hybridauth = new Hybridauth($config);

            $this->profiles[$name] = $hybridauth->getAdapter($provider->getName());

            return $hybridauth->authenticate($provider->getName()) != null;
        }

        return false;
    }

    /**
     * @param $name
     *
     * @return \Hybridauth\Adapter\AdapterInterface|null
     */
    public function getAdapter($name) {
        return isset($this->profiles) ? $this->profiles[$name] : null;
    }
}
