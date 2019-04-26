<?php

namespace Oforge\Engine\Modules\Core\Forge;

use Error;
use Exception;
use Slim\App as SlimApp;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;

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
        $container = $this->getContainer();

        $errorHandler = function ($container) {
            return function (Request $request, Response $response, $exception) use ($container) {
                /** @var Exception|Error $exception */
                $message = $exception->getMessage();
                $trace   = str_replace("\n", "<br />\n", $exception->getTraceAsString());
                $file    = $exception->getFile();
                $line    = $exception->getLine();
                $html    = <<<TAG
<h1>Exception: $message</h1>
<dl>
    <dt><strong>File</strong></dt><dd>$file</dd>
    <dt><strong>Line</strong></dt><dd>$line</dd>
    <dt><strong>Trace</strong></dt><dd>$trace</dd>
</dl>
TAG;

                return $response->withStatus(500)->withHeader('Content-Type', 'text/html')->write($html);
            };
        };

        $container['errorHandler']    = $errorHandler;
        $container['phpErrorHandler'] = $errorHandler;

        $container['cookie'] = function ($container) {
            return new Cookies();
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
