<?php

namespace SocialLogin\Services;

use Hybridauth\Hybridauth;

/**
 * Class LoginProviderService
 *
 * @package SocialLogin\Services
 */
class LoginConnectService {
    /** @var array $profiles */
    private $profiles = [];

    public function connect(string $name, string $callback) : bool {
        /** @var LoginProviderService $service */
        $service  = Oforge()->Services()->get('sociallogin.providers');
        $provider = $service->getByName($name);

        if ($provider !== null) {
            $configElement = ['enabled' => true, 'keys' => []];

            if ($provider->getAppId() !== null) {
                $configElement['keys']['id'] = $provider->getAppId();
            }

            if ($provider->getSecret() !== null) {
                $configElement['keys']['secret'] = $provider->getSecret();
            }

            if ($provider->getAppKey() !== null) {
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
