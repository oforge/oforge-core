<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\Media\Twig\MediaExtension;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\CustomTwig;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigOforgeDebugExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\AccessExtension;
use Oforge\Engine\Modules\TemplateEngine\Extensions\Twig\SlimExtension;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

class MailService {

    /**
     * Initialises PHP Mailer Instance with specified Mailer Options and TemplateData.
     * Options = [
     * 'to'         => ['user@host.de' => 'user_name', user2@host.de => 'user2_name, ...],
     * 'cc'         => [],
     * 'bcc'        => [],
     * 'replyTo'    => [],
     * 'attachment' => [],
     * "subject"    => string,
     * "html"       => bool,
     * ]
     *
     * TemplateData = ['key' = value, ... ]

     * @param array $options
     * @param array $templateData
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function send(array $options, array $templateData = []) {
        if ($this->isValid($options)) {
            try {
                /**
                 * @var $configService ConfigService
                 */
                $configService = Oforge()->Services()->get("config");
                $exceptions    = $configService->get("mailer_exceptions");

                /** @var  $mail */
                $mail          = new PHPMailer($exceptions);

                /**  Mailer Settings */
                $mail->isSMTP();
                $mail->setFrom($this->getSenderAddress($options['from']));
                $mail->Host       = $configService->get("mailer_host");
                $mail->Username   = $configService->get("mailer_smtp_username");
                $mail->Port       = $configService->get("mailer_port");
                $mail->SMTPAuth   = $configService->get("mailer_smtp_auth");
                $mail->Password   = $configService->get("mailer_smtp_password");
                $mail->SMTPSecure = $configService->get("mailer_smtp_secure");
                $mail->Encoding = 'base64';
                $mail->CharSet = 'UTF-8';

                /** Add Recipients ({to,cc,bcc}Addresses) */
                foreach ($options["to"] as $key => $value) {
                    $mail->addAddress($key, $value);
                }
                if (isset($options['cc'])) {
                    foreach ($options["cc"] as $key => $value) {
                        $mail->addCC($key, $value);
                    }
                }
                if (isset($options['bcc'])) {
                    foreach ($options["bcc"] as $key => $value) {
                        $mail->addBCC($key, $value);
                    }
                }
                if (isset($options['replyTo'])) {
                    foreach ($options["replyTo"] as $key => $value) {
                        $mail->addReplyTo($key, $value);
                    }
                }

                /** Add Attachments: */
                if (isset($options['attachment'])) {
                    foreach ($options["attachment"] as $key => $value) {
                        $mail->addAttachment($key, $value);
                    }
                }
                /** Generate Base-Url for Media */
                $conversationLink = 'http://';
                if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                    $conversationLink = 'https://';
                }

                $conversationLink .= $_SERVER['HTTP_HOST'];
                $templateData['baseUrl'] = $conversationLink;

                /** Render HTML */
                $renderedTemplate = $this->renderMail($options,$templateData);


                /** Add Content */
                $mail->isHTML(ArrayHelper::get($options, 'html', true));
                $mail->Subject = $options["subject"];
                $mail->Body    = $renderedTemplate;

                $mail->send();

                Oforge()->Logger()->get("mailer")->info("Message has been sent", $templateData);
                return true;

            } catch (Exception $e) {
                Oforge()->Logger()->get("mailer")->error("Message has not been sent", [$mail->ErrorInfo]);
                return false;
            }
        }
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    private function isValid(array $options) : bool {

        $keys = ["to", "subject", "template"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }

        /** Validate Mail Addresses */
        $emailKeys = ["to", "cc", "bcc", "replyTo"];
        foreach ($emailKeys as $key) {
            if (array_key_exists($key, $options)) {
                if (is_array($options[$key])) {
                    foreach ($options[$key] as $email => $name) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            throw new InvalidArgumentException("$email is not a valid email.");
                        }
                    }
                } else {
                    // Argument is not an Array
                    throw new InvalidArgumentException("Expected array for $key but get " . gettype($options[$key]));
                }
            }
        }
        return true;
    }

    /**
     * Loads minimal Twig Environment and returns rendered Template from active Theme.
     * If specified Template does not exists in active Theme -> Fallback to Base Theme
     *
     * @param array $options
     * @param array $templateData
     *
     * @return string
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function renderMail(array $options, array $templateData) {



        $templateManagementService = Oforge()->Services()->get("template.management");
        $templateName = $templateManagementService->getActiveTemplate()->getName();
        $templatePath = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR . 'MailTemplates';

        if(!file_exists($templatePath . DIRECTORY_SEPARATOR . $options['template'])) {
            $templatePath = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . Statics::DEFAULT_THEME . DIRECTORY_SEPARATOR . 'MailTemplates';
        }

        $twig = new CustomTwig($templatePath);
        $twig->addExtension(new \Oforge\Engine\Modules\CMS\Twig\AccessExtension());
        $twig->addExtension(new AccessExtension());
        $twig->addExtension(new MediaExtension());
        $twig->addExtension(new SlimExtension());
        $twig->addExtension(new TwigOforgeDebugExtension());

        return $twig->fetch($template = $options['template'], $data = $templateData);
    }

    /**
     * Looks up mailer backend options for custom sender and host information.
     *
     * @param string $key
     *
     * @return string
     * @throws ConfigElementNotFoundException
     * @throws ServiceNotFoundException
     */
    public function getSenderAddress($key = 'info') {

        $configService = Oforge()->Services()->get("config");

        $host          = $configService->get('mailer_from_host');
        if(!$host) {
            throw new InvalidArgumentException("Error: Host is not set");
        }
        $sender        = $configService->get('mailer_from_' . $key);

        $senderAddress = $sender . '@' . $host;
        return $senderAddress;
    }

    public function batchSend() {
        //
    }

    public function getSystemMails() {
        //
    }
}
