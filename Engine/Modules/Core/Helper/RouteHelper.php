<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
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
    /** @var string $basePath */
    private static $basePath;

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * @param Response $response
     * @param string|null $routeName If null, the routeName by meta.route.name.
     * @param array $namedParams
     * @param array $queryParams
     *
     * @return Response
     */
    public static function redirect(Response $response, ?string $routeName = null, array $namedParams = [], array $queryParams = []) : Response {
        self::init();
        if ($routeName === null) {
            $routeName = Oforge()->View()->get('meta.route.name');
        }
        $uri = self::getSlimUrl($routeName, $namedParams, $queryParams);

        return $response->withRedirect($uri, 302);
    }

    /**
     * Get url of base url & relative path.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getFullUrl(string $url) {
        if (StringHelper::startsWith($url, '/')) {
            if (!isset(self::$baseUrl)) {
                self::$baseUrl = Oforge()->View()->get('meta.route.baseUrl');
            }
            $url = StringHelper::leading($url, '/');

            return self::$baseUrl . $url;
        }

        return $url;
    }

    /**
     * Get absolute URL based on www root folder.
     *
     * @param string $url
     *
     * @return string
     */
    public static function getUrlWithBasePath(string $url) {
        if (StringHelper::startsWith($url, '/')) {
            if (!isset(self::$basePath)) {
                self::$basePath = Oforge()->View()->get('meta.route.basePath');
            }

            return self::$basePath . $url;
        }

        return $url;
    }

    /**
     * Get url for route, possible modified by services.
     *
     * @param string $routeName
     * @param array $namedParams
     * @param array $queryParams
     *
     * @return mixed|string
     * @throws ServiceNotFoundException
     */
    public static function getUrl(string $routeName, array $namedParams = [], array $queryParams = []) {
        /** @var UrlService $urlService */
        $urlService = Oforge()->Services()->get('url');

        return $urlService->getUrl($routeName, $namedParams, $queryParams);
    }

    /**
     * Get slim registered url for route.
     *
     * @param string $routeName
     * @param array $namedParams
     * @param array $queryParams
     *
     * @return string
     */
    public static function getSlimUrl(string $routeName, array $namedParams = [], array $queryParams = []) {
        self::init();

        return self::$router->pathFor($routeName, $namedParams, $queryParams);
    }

    /**
     * Parse an url to an array of two keys ['url'] and ['query_params'] in a slim readable format
     *
     * @param $url
     *
     * @return array an array with two keys ['url'] and ['query_params']
     */
    public static function parseUrlWithQueryParams(string $url) : array {
        $queryParams        = ['url' => '', 'query_params' => []];
        $queryParams['url'] = parse_url($url, PHP_URL_PATH);
        parse_str(parse_url($url, PHP_URL_QUERY), $queryParams['query_params']);

        return $queryParams;
    }

    private static function init() {
        if (!isset(self::$router)) {
            self::$router = Oforge()->App()->getContainer()->get('router');
        }
    }
}
