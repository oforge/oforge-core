<?php

namespace Oforge\Engine\Modules\Core\Forge;

use Error;
use Exception;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Services\Session\SessionManagementService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use Slim\Exception\InvalidMethodException;
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


    /**
     * Start the session
     *
     * @param int $lifetimeSeconds
     * @param string $path
     * @param null $domain
     * @param null $secure
     */
    private function sessionStart($lifetimeSeconds = 0, $path = '/', $domain = null, $secure = null)
    {
        $sessionStatus = session_status();

        if ($sessionStatus != PHP_SESSION_ACTIVE) {
            session_name( "oforge_session" );
            if ( ! empty( $_SESSION['deleted_time'] ) &&
                 $_SESSION['deleted_time'] < time() - 180 ) {
                session_destroy();
            }
            // Set the domain to default to the current domain.
            $domain = isset( $domain ) ? $domain : $_SERVER['SERVER_NAME'];

            // Set the default secure value to whether the site is being accessed with SSL
            $secure = isset( $secure ) ? $secure : isset( $_SERVER['HTTPS'] ) ? true : false;

            // Set the cookie settings and start the session
            session_set_cookie_params( $lifetimeSeconds, $path, $domain, $secure, true );
            session_start();
            $_SESSION['created_time'] = time();
        }
    }

    public function returnCachedResult($silent = false) : bool {
        $this->sessionStart();

        /**
         * @var $response ResponseInterface
         */
        $response = $this->getContainer()->get('response');

        /**
         * @var $request ServerRequestInterface
         */
        $request = $this->getContainer()->get('request');

        $mode            = Oforge()->Settings()->get("mode");
        $output          = null;
        $filename        = str_replace("/", "_", $request->getUri()->__toString());
        $userNotLoggedIn = !isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] != 1;

        if ($mode != "development") {
            if ($userNotLoggedIn) {
                $files = glob(ROOT_PATH . Statics::RESULT_CACHE_DIR . DIRECTORY_SEPARATOR . $filename . "*");



                foreach ($files as $file) {

                    //TODO add timestamp
                    if (strpos($file, "##") === false) {
                        $output = file_get_contents($file);

                        $outputBuffering = $this->getContainer()->get('settings')['outputBuffering'];
                        if ($outputBuffering === 'prepend') {
                            // prepend output buffer content
                            $body = new Http\Body(fopen('php://temp', 'r+'));
                            $body->write($output . $response->getBody());
                            $response = $response->withBody($body);
                        } elseif ($outputBuffering === 'append') {
                            // append output buffer content
                            $response->getBody()->write($output);
                        }

                        $response = $this->finalize($response);

                        if (!$silent) {
                            $this->respond($response);
                        }

                        return true;
                    }
                }
            }
        }

        return false;

    }

    public function run($silent = false) {
        /**
         * @var $response ResponseInterface
         */
        $response = $this->getContainer()->get('response');

        /**
         * @var $request ServerRequestInterface
         */
        $request = $this->getContainer()->get('request');

        $mode            = Oforge()->Settings()->get("mode");
        $output          = null;
        $filename        = str_replace("/", "_", $request->getUri()->__toString());
        $userNotLoggedIn = !isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] != 1;

        try {
            ob_start();
            $response = $this->process($request, $response);
        } catch (InvalidMethodException $e) {
            $response = $this->processInvalidMethod($e->getRequest(), $response);
        } finally {
            $output = ob_get_clean();
        }

        $outputBuffering = $this->getContainer()->get('settings')['outputBuffering'];
        if ($outputBuffering === 'prepend') {
            // prepend output buffer content
            $body = new Http\Body(fopen('php://temp', 'r+'));
            $body->write($output . $response->getBody());
            $response = $response->withBody($body);
        } elseif ($outputBuffering === 'append') {
            // append output buffer content
            $response->getBody()->write($output);
        }

        $response = $this->finalize($response);

        if (!$silent) {
            $this->respond($response);
        }

        $cache = Oforge()->View()->get("cache-for");

        if ($userNotLoggedIn && $cache != null && is_string($cache) && $mode != "development") {
            @mkdir(ROOT_PATH . Statics::RESULT_CACHE_DIR, 0777, true);

            file_put_contents(ROOT_PATH . Statics::RESULT_CACHE_DIR . DIRECTORY_SEPARATOR . $filename . "##" . $cache, $response->getBody()->__toString());
        }

        return $response;
    }


}
