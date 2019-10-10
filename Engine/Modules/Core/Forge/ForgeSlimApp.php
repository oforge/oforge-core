<?php

namespace Oforge\Engine\Modules\Core\Forge;

use Error;
use Exception;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;
use Slim\Exception\InvalidMethodException;
use Slim\Http\Cookies;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer\PHPMailer\PHPMailer;

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
        $container    = $this->getContainer();
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
                /**
                 * Send error report via mail
                 */
                if (Oforge()->Settings()->get('error_mail_report')['active']) {
                    $this->sendReportMail($html);
                }

                if (Oforge()->Settings()->isDevelopmentMode()) {
                    return $response->withStatus(500)->withHeader('Content-Type', 'text/html')->write($html);
                } else {
                    Oforge()->Logger()->get()->error($message, $exception->getTrace());

                    /** @var TemplateManagementService $templateManagementService */
                    $templateManagementService = Oforge()->Services()->get("template.management");
                    $activeTheme = $templateManagementService->getActiveTemplate()->getName();

                    $activeTheme500 = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $activeTheme . DIRECTORY_SEPARATOR . "500.html";
                    if (file_exists($activeTheme500)) {
                        return $response->withRedirect($activeTheme500, 307);
                       } else {
                        return $response->withRedirect(Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . Statics::DEFAULT_THEME . DIRECTORY_SEPARATOR . "500.html", 307);
                    }
                }
            };
        };

        $container['errorHandler']    = $errorHandler;
        $container['phpErrorHandler'] = $errorHandler;

        $container['cookie'] = function ($container) {
            return new Cookies();
        };
    }

    /**
     * @param array $log
     *
     * @return string
     */
    private function parseLogs(array $log) {
        $message = '';
        foreach ($log as $key => $value) {
            $message .= $key . ' => ' . $value . "\n";
        }

        return $message;
    }

    /**
     * @param $html
     *
     * @throws \PHPMailer\PHPMailer\Exception
     */
    private function sendReportMail(string &$html) {
        $mailer_settings = Oforge()->Settings()->get('error_mail_report')['mailer_settings'];

        /** @var PHPMailer $mailer */
        $mailer = new PHPMailer(true); // throw exceptions on errors
        $mailer->isSMTP();
        $mailer->SMTPAuth  = true;
        $mailer->Host      = $mailer_settings['smtp_host'];
        $mailer->Username  = $mailer_settings['smtp_user'];
        $mailer->Password  = $mailer_settings['smtp_pw'];
        $mailer->Port      = $mailer_settings['smtp_port'];
        $mailer->SMTPDebug = 4;

        $mailer->addStringAttachment($this->parseLogs($_SERVER), 'server.log');
        $mailer->addStringAttachment($this->parseLogs($_SESSION), 'session.log');
        $mailer->addStringAttachment($html, 'error.html');


        $mailer->Subject = 'oforge error 500';
        $mailer->setFrom($mailer_settings['smtp_from'], 'Oforge');
        $mailer->addAddress($mailer_settings['receiver_address'], 'Dev');
        $mailer->Body = 'error error error';

        try {
            $mailer->send();

        } catch (Exception $e) {
            // append mailer error to html output
            if (Oforge()->Settings()->isDevelopmentMode()) {
                $html .= <<<Tag
<h1>Could not send mail report:</h1>
<dl>
    <dt>$e</dt>
</dl>
Tag;
                }


        }
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
    public function sessionStart($lifetimeSeconds = 0, $path = '/', $domain = null, $secure = null) {
        $sessionStatus = session_status();

        if ($sessionStatus != PHP_SESSION_ACTIVE) {
            session_name("oforge_session");
            if (!empty($_SESSION['deleted_time'])
                && $_SESSION['deleted_time'] < time() - 180) {
                session_destroy();
            }
            // Set the domain to default to the current domain.
            $domain = isset($domain) ? $domain : $_SERVER['SERVER_NAME'];

            // Set the default secure value to whether the site is being accessed with SSL
            $secure = isset($secure) ? $secure : isset($_SERVER['HTTPS']) ? true : false;

            // Set the cookie settings and start the session
            session_set_cookie_params($lifetimeSeconds, $path, $domain, $secure, true);
            session_start();
            $_SESSION['created_time'] = time();
        }
    }

    public function returnCachedResult($silent = false) : bool {
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
                    $output = null;
                    if (strpos($file, "##") === false) {
                        $output = file_get_contents($file);

                    } else {
                        $split = explode("##", $file);
                        if (sizeof($split) == 2) {
                            $durationString = $split[1];
                            try {
                                $interval = new \DateInterval('P' . $durationString);
                                $date     = new \DateTime(date('c', filemtime($file)));

                                $expiresDate = $date->add($interval);
                                $now         = new \DateTime();

                                if ($expiresDate > $now) {
                                    // file expires in the future
                                    $output = file_get_contents($file);
                                }
                            } catch (Exception $exception) {
                                print_r($exception);
                                Oforge()->Logger()->get()->error($exception->getMessage(), $exception->getTrace());
                            }
                        }
                    }

                    if ($output != null) {
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
            @mkdir(ROOT_PATH . Statics::RESULT_CACHE_DIR, 0755, true);

            file_put_contents(ROOT_PATH . Statics::RESULT_CACHE_DIR . DIRECTORY_SEPARATOR . $filename . "##" . $cache, $response->getBody()->__toString());
        }

        return $response;
    }

}
