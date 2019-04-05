<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigEnvironment;
use PHPMailer\PHPMailer\PHPMailer;
use Oforge\Engine\Modules\TemplateEngine\Core\Twig\TwigFileSystemLoader;

class MailService {

    /**
     * Initialises PHP Mailer Instance with specified Mailer Settings, Options and TemplateData
     *
     * Options = ['to' => [], 'cc' => [], 'bcc' => [], 'replyTo' => [], 'attachment' => [], "subject" => string "html" => bool]
     *
     * Template Data = ['key_1' => value, ...]
     *
     * @param array $options
     * @param array $templateData
     *
     * @throws ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \PHPMailer\PHPMailer\Exception
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function send(array $options, array $templateData = []) {
        if ($this->isValid($options)) {
            try {
                /**
                 * @var $configService ConfigService
                 */
                $configService = Oforge()->Services()->get("config");
                $mail          = new PHPMailer(true);

                /** Set Server Settings */
                $mail->isSMTP();
                $mail->setFrom($configService->get("mailer_from"));
                $mail->Host       = $configService->get("mailer_host");
                $mail->Username   = $configService->get("mailer_username");
                $mail->Port       = $configService->get("mailer_port");
                $mail->SMTPAuth   = $configService->get("mailer_smtp_auth");
                $mail->Password   = $configService->get("mailer_smtp_password");
                $mail->SMTPSecure = $configService->get("mailer_smtp_secure");

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

                /** Render Mail Template */
                $templateData = [
                    'mail' => $options,
                    'data' => $templateData
                ];
                $renderedTemplate = $this->renderMail($templateData);


                /** Add Content */
                $mail->isHTML(ArrayHelper::get($options, 'html', true));
                $mail->Subject = $options["subject"];
                $mail->Body    = $renderedTemplate;
                $mail->send();

                Oforge()->Logger()->get("mailer")->info("Message has been sent", $options);
            } catch (Exception $e) {
                Oforge()->Logger()->get("mailer")->error("Message has not been sent", [$mail->ErrorInfo]);
            }
        }
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExists
     */
    private function isValid(array $options) : bool {

        $keys = ["to", "subject", "template"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExists($key);
            }
        }

        /** Validate Mail Addresses */
        $emailKeys = ["to", "cc", "bcc", "replyTo"];
        foreach ($emailKeys as $key) {
            if (array_key_exists($key, $options)) {
                if (is_array($options[$key])) {
                    foreach ($options[$key] as $email => $name) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            throw new \InvalidArgumentException("$email is not a valid email.");
                        }
                    }
                } else {
                    // Argument is not an Array
                    throw new \InvalidArgumentException("Expected array for $key but get " . gettype($options[$key]));
                }
            }
        }
        return true;
    }

    /**
     * Renders the HTML from specified template
     * @param array $data
     *
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function renderMail(array $data) {

        $loader = new TwigFileSystemLoader(Statics::MAIL_TEMPLATE_DIR);
        $twig = new TwigEnvironment($loader);
        $template = $twig->load($data['mail']['template']);

        return $template->render($data['data']);
    }

}