<?php

namespace Oforge\Engine\Modules\TemplateEngine\Extensions\Services;

use Slim\Router;

/**
 * Class UrlService
 *
 * @package Oforge\Engine\Modules\TemplateEngine\Extensions\Services
 */
class UrlService {
    /** @var Router $router */
    private $router;

    /**
     * @param mixed ...$vars
     *
     * @return mixed|string
     */
    public function getUrl(string $name, array $namedParams = [], array $queryParams = []) {
        if (!isset($this->router)) {
            $this->router = Oforge()->App()->getContainer()->get('router');
        }
        try {
            $result = $this->router->pathFor($name, $namedParams, $queryParams);
        } catch (\Exception $exception) {
            $result = $name;
        }

        return $result;
    }

}


