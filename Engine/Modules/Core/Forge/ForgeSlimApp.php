<?php

namespace Oforge\Engine\Modules\Core\Forge;

use Slim\App as SlimApp;

/**
 * Class App
 * An extension of the SlimApp Container.
 * See https://www.slimframework.com/
 *
 * @package Oforge\Engine\Modules\Core
 */
class ForgeSlimApp extends SlimApp {
    /** @var ForgeSlimApp $instance */
    protected static $instance = null;

    /**
     * App constructor.
     * Defines the slim default error behaviour.
     */
    public function __construct() {
        parent::__construct();
        $c                    = $this->getContainer();
        $c['errorHandler']    = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                return $c['response']->withStatus(500)->withHeader('Content-Type', 'text/html')->write($exception);
            };
        };
        $c['phpErrorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                return $c['response']->withStatus(500)->withHeader('Content-Type', 'text/html')->write($exception);
            };
        };
        $c['cookie']          = function ($c) {
            return new \Slim\Http\Cookies();
        };
    }

    /**
     * @return ForgeSlimApp
     */
    public static function getInstance() : ForgeSlimApp {
        if (!isset(self::$instance)) {
            self::$instance = new ForgeSlimApp();
        }

        return self::$instance;
    }

}
