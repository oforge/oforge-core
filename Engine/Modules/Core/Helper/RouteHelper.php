<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Oforge\Engine\Modules\TemplateEngine\Extensions\Services\UrlService;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class RouteHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class RouteHelper {
    /** @var Router $router */
    private static $router;
    /** @var string $baseUrl */
    private static $baseUrl;

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * @param Response $response
     * @param string|null $routeName If null, the routeName by meta.route.name.
     * @param array $urlParams
     * @param array $queryParams
     *
     * @return Response
     */
    public static function redirect(Response $response, ?string $routeName = null, array $urlParams = [], array $queryParams = []) : Response {
        self::init();
        if (is_null($routeName)) {
            $routeName = Oforge()->View()->get('meta.route.name');
        }
        $uri = self::$router->pathFor($routeName, $urlParams, $queryParams);

        return $response->withRedirect($uri, 302);
    }

    /**
     * Get url of base url & relative path.
     *
     * @param string $relativePath
     *
     * @return string
     */
    public static function getFullUrl(string $relativePath) {
        if (!isset(self::$baseUrl)) {
            self::$baseUrl = Oforge()->View()->get('meta.route.baseUrl');
        }
        $relativePath = str_replace('\\', '/', $relativePath);
        $relativePath = StringHelper::leading($relativePath, '/');

        return self::$baseUrl . $relativePath;
    }

    /**
     * @param string $name
     * @param array $namedParams
     * @param array $queryParams
     *
     * @return mixed|string
     */
    public static function getUrl(string $name, array $namedParams = [], array $queryParams = []) {
        /** @var UrlService $urlService */
        $urlService = Oforge()->Services()->get('url');

        return $urlService->getUrl($name);
    }

    private static function init() {
        if (!isset(self::$router)) {
            self::$router = Oforge()->App()->getContainer()->get('router');
        }
    }

}
