<?php

namespace Oforge\Engine\Modules\Core\Helper;

use Slim\Http\Response;
use Slim\Router;

/**
 * Class RedirectHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class RedirectHelper {
    /** @var Router $router */
    private static $router;

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
            $routeName = Oforge()->View()->get('meta')['route']['name'];
        }
        $uri = self::$router->pathFor($routeName, $urlParams, $queryParams);

        return $response->withRedirect($uri, 302);
    }

    private static function init() {
        if (!isset(self::$router)) {
            self::$router = Oforge()->App()->getContainer()->get('router');
        }
    }

}
