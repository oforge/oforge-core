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
     * @param string $name
     * @param array $namedParams
     * @param array $queryParams
     *
     * @return string
     */
    public function getUrl(string $name, array $namedParams = [], array $queryParams = []) : string {
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


