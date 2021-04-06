<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Models\Plugin\Plugin;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Media\Twig\MediaExtension;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\CustomTwig;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigOforgeDebugExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\AccessExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\SlimExtension;
use PHPMailer\PHPMailer\PHPMailer;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;
use Twig_Loader_Filesystem;

/**
 * Class MailService
 *
 * @package Oforge\Engine\Modules\Mailer\Services
 */
class MailService
{
    private $mailerTwig;
    private $cacheBuildFrom = [];

    /**
     * Initialises PHP Mailer instance with specified mailer options and template data.
     *      Config = [
     *           'from'       => ['mail' => '...', 'name' => '...'],
     *           'to'         => ['user@host.de' => 'user_name', user2@host.de => 'user2_name, ...],
     *           'cc'         => [], // like 'to'
     *           'bcc'        => [], // like 'to'
     *           'replyTo'    => [], // like 'to'
     *           'attachment' => [path => filename],
     *           'subject'    => string,
     *           'template'   => string, // Twig template file name (with extension)
     *           'html'       => string, // Html body
     *           'text'       => string, // Raw text mail
     *      ]
     * TemplateData = ['key' = value, ... ]
     *
     * @param array $config
     * @param array $templateData
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    public function send(array $config, array $templateData = []) : bool
    {
        $this->validateConfig($config);
        try {
            /** @var ConfigService $configService */
            $configService         = Oforge()->Services()->get('config');
            $configThrowExceptions = $configService->get('mailer_throw_exceptions');

            $mailer = new PHPMailer($configThrowExceptions);
            $mailer->isSMTP();
            // $mailer->SMTPDebug = $configService->get('mailer_smtp_debug');// TODO missing yet
            $mailer->Host       = $configService->get('mailer_smtp_host');
            $mailer->Username   = $configService->get('mailer_smtp_username');
            $mailer->Port       = $configService->get('mailer_smtp_port');
            $mailer->SMTPAuth   = $configService->get('mailer_smtp_auth');
            $mailer->Password   = $configService->get('mailer_smtp_password');
            $mailer->SMTPSecure = $configService->get('mailer_smtp_secure');
            $mailer->Encoding   = 'base64';
            $mailer->CharSet    = 'UTF-8';

            //region # addresses
            if (isset($config['from'])) {
                $mailer->setFrom($config['from']['mail'], $config['from']['name'] ?? '');
            }

            $devRedirectEnabled = $configService->get('mailer_dev_redirect_enabled');
            if ($devRedirectEnabled === true) {
                $devRedirectTo = $configService->get('mailer_dev_redirect_to');
                if (empty($devRedirectTo)) {
                    Oforge()->Logger()->get('mailer')->error('Could not redirect message, recipient not set in mailer backend options!');
                } else {
                    $mailer->addAddress($devRedirectTo, 'dev');
                }
            } else {
                foreach ($config['to'] as $key => $value) {
                    $mailer->addAddress($key, $value);
                }
            }
            $headers = [
                'cc'      => 'addCC',
                'bcc'     => 'addBCC',
                'replyTo' => 'addReplyTo',
            ];
            foreach ($headers as $headerKey => $headerMethod) {
                if (isset($config[$headerKey])) {
                    foreach ($config[$headerKey] as $key => $value) {
                        $mailer->$headerMethod($key, $value);
                        // $mailer->addCC($key, $value);
                        // $mailer->addBCC($key, $value);
                        // $mailer->addReplyTo($key, $value);
                    }
                }
            }
            //endregion

            // Attachments
            if (isset($config['attachment'])) {
                foreach ($config['attachment'] as $key => $value) {
                    $mailer->addAttachment($key, $value);
                }
            }
            // BaseUrl for Media
            if ( !isset($templateData['baseUrl'])) {
                $baseUrl = Oforge()->View()->get('meta.route.baseUrl');
                if ($baseUrl === null) {
                    $conversationLink = 'http://';
                    if ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                        $conversationLink = 'https://';
                    }
                    if (isset($_SERVER['HTTP_HOST'])) {
                        $conversationLink        .= $_SERVER['HTTP_HOST'];
                        $templateData['baseUrl'] = $conversationLink;
                    }
                } else {
                    $templateData['baseUrl'] = $baseUrl;
                }
            }

            $mailer->isHTML(true);
            if (isset($config['template'])) {
                /** Render HTML */
                $body = $this->renderTemplate($config, $templateData);
            } elseif (isset($config['html'])) {
                $body = $config['html'];
            } elseif (isset($config['text'])) {
                $mailer->isHTML(false);
                $body = $config['text'];
            } else {
                $body = '';
            }
            $mailer->Body    = $body;
            $mailer->Subject = $config['subject'];

            $success = $mailer->send();

            if ($devRedirectEnabled == true) {
                Oforge()->Logger()->get('mailer')->info('Message has been redirected', [$mailer->getToAddresses(), $config, $templateData]);
            } else {
                Oforge()->Logger()->get('mailer')->info('Message has been sent', [$config, $templateData]);
            }

            return $success;
        } catch (Exception $exception) {
            Oforge()->Logger()->get('mailer')->error('Message has not been sent', [isset($mailer) ? $mailer->ErrorInfo : $exception->getMessage()]);

            return false;
        }
    }

    /**
     * Looks up mailer backend options for custom sender and host information.
     *
     * @param string $configFromMailPrefix
     *
     * @return array
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function buildFromConfigByPrefix($configFromMailPrefix = 'info') : array
    {
        $configService = Oforge()->Services()->get('config');

        return [
            'mail' => $this->buildFromMailByPrefix($configFromMailPrefix),
            'name' => $configService->get('mailer_from_builder_name') ?? '',
        ];
    }

    /**
     * Looks up mailer backend options for custom sender and host information.
     *
     * @param string $configFromMailPrefix
     *
     * @return string
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function buildFromMailByPrefix($configFromMailPrefix = 'info') : string
    {
        if ( !isset($this->cacheBuildFrom[$configFromMailPrefix])) {
            $configService = Oforge()->Services()->get('config');

            $mailHost = $configService->get('mailer_from_builder_host');
            if (empty($mailHost)) {
                throw new InvalidArgumentException("Error: Builder value 'mailHost' is not set");
            }
            $mailPrefix = $configService->get('mailer_from_builder_mail_prefix_' . $configFromMailPrefix);
            if (empty($mailPrefix)) {
                throw new InvalidArgumentException("Error: Builder value 'mailPrefix' is not set");
            }
            $this->cacheBuildFrom[$configFromMailPrefix] = ($mailPrefix . '@' . $mailHost);
        }

        return $this->cacheBuildFrom[$configFromMailPrefix];
    }

    /**
     * Loads minimal twig environment and returns rendered HTML-template with inlined CSS from active theme.
     * If specified template does not exists in active theme -> fallback to base theme
     *
     * @param array $config
     * @param array $templateData
     *
     * @return string
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @throws OptimisticLockException
     * @throws TemplateNotFoundException
     */
    protected function renderTemplate(array $config, array $templateData) : string
    {
        if ( !isset($this->mailerTwig)) {
            /** @var TemplateManagementService $templateManagementService */
            $templateManagementService = Oforge()->Services()->get('template.management');
            $templateName              = $templateManagementService->getActiveTemplate()->getName();
            // $templatePath              = Statics::TEMPLATE_DIR . Statics::GLOBAL_SEPARATOR . $templateName . Statics::GLOBAL_SEPARATOR . 'MailTemplates';
            //
            // if ( !file_exists($templatePath . Statics::GLOBAL_SEPARATOR . $config['template'])) {
            //     $templatePath = Statics::TEMPLATE_DIR . Statics::GLOBAL_SEPARATOR . Statics::DEFAULT_THEME . Statics::GLOBAL_SEPARATOR . 'MailTemplates';
            // }
            // //TODO Problem: aktuell keine MailTemplates per Plugins möglich!!!
            //TODO REFACTORING: Generic Twig Instance by TemplateRenderService method

            $paths            = [
                Twig_Loader_Filesystem::MAIN_NAMESPACE => [],
            ];
            $mainPaths        = [];
            $baseThemePath    = join(Statics::GLOBAL_SEPARATOR, [ROOT_PATH, Statics::TEMPLATE_DIR, Statics::DEFAULT_THEME, 'MailTemplates']);
            $currentThemePath = join(Statics::GLOBAL_SEPARATOR, [ROOT_PATH, Statics::TEMPLATE_DIR, $templateName, 'MailTemplates']);
            $mainPaths[]      = $currentThemePath;
            try {
                /** @var Plugin[] $plugins */
                $plugins = Oforge()->Services()->get('plugin.access')->getActive();
                foreach ($plugins as $plugin) {
                    $pluginName = $plugin->getName();
                    $pluginPath = join(Statics::GLOBAL_SEPARATOR, [ROOT_PATH, Statics::PLUGIN_DIR, $pluginName, Statics::VIEW_DIR, 'MailTemplates']);
                    if (file_exists($pluginPath)) {
                        $mainPaths[]        = $pluginPath;
                        $tmp                = $paths[$pluginName] ?? [];
                        $tmp[]              = $pluginPath;
                        $paths[$pluginName] = $tmp;
                    }
                }
            } catch (Exception $exception) {
                Oforge()->Logger()->logException($exception);
            }
            $mainPaths[] = $baseThemePath;

            $paths[Twig_Loader_Filesystem::MAIN_NAMESPACE] = $mainPaths;

            // $twig = new CustomTwig($templatePath, ['cache' => ROOT_PATH . Statics::GLOBAL_SEPARATOR . Statics::CACHE_DIR . '/mailer']);
            $twig = new CustomTwig(
                $paths,#
                [
                    'cache' => join(Statics::GLOBAL_SEPARATOR, [ROOT_PATH, Statics::CACHE_DIR, 'mailer']),
                ]
            );
            try {
                Oforge()->Services()->get('cms');
                $cmsTwigExtension = '\CMS\Twig\CmsTwigExtension';
                $twig->addExtension(new $cmsTwigExtension());
            } catch (Exception $exception) {
                // nothing to do
            }
            $twig->addExtension(new AccessExtension());
            $twig->addExtension(new MediaExtension());
            $twig->addExtension(new SlimExtension());
            $twig->addExtension(new TwigOforgeDebugExtension());

            $this->mailerTwig = $twig;
        }
        $html = $this->mailerTwig->fetch($config['template'], $templateData);
        /** @var InlineCssService $inlineCssService */
        $inlineCssService = Oforge()->Services()->get('mail.inlineCss');

        return $inlineCssService->inline($html);
    }

    /**
     * @param array $config
     *
     * @throws ConfigOptionKeyNotExistException
     */
    protected function validateConfig(array &$config)
    {
        // required fields
        $keys = ['to', 'subject'];
        foreach ($keys as $key) {
            if ( !isset($config[$key])) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }

        if (isset($config['from'])) {
            $tmp = $config['from'];
            if (is_string($tmp)) {
                $tmp = ['mail' => $tmp, 'name' => ''];
            }
            $tmp['name'] = $tmp['name'] ?? $tmp['mail'];
            $keys        = ['mail', 'name'];
            foreach ($keys as $key) {
                if ( !isset($tmp[$key])) {
                    throw new ConfigOptionKeyNotExistException($key);
                }
            }
            if ( !filter_var($tmp['mail'], FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException(
                    sprintf("Value of config 'from.mail' is not valid valid email ('%s').", $tmp['mail'])
                );
            }
            $config['from'] = $tmp;
        }

        /** Validate Mail Addresses */
        $keys = ['to', 'cc', 'bcc', 'replyTo'];
        foreach ($keys as $key) {
            if (isset($config[$key])) {
                if (is_string($config[$key])) {
                    $config[$key] = [
                        $config[$key] => $config[$key],
                    ];
                }
                foreach ($config[$key] as $email => $name) {
                    if ( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new InvalidArgumentException("Value of config '$key' has an not valid valid email ('$email').");
                    }
                }
            }
        }
    }

}
