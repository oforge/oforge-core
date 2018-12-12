<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use PHPMailer\PHPMailer\PHPMailer;

class MailService
{
    /**
     * @param array $options
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(array $options)
    {
        if ($this->isValid($options)) {

            try {
                /**
                 * @var $configService ConfigService
                 */
                $configService = Oforge()->Services()->get("config");
                $mail = new PHPMailer(true);

                /**
                 * Set Server Settings
                 */
                $mail->isSMTP();
                $mail->setFrom($configService->get("mailer.from"));
                $mail->Host = $configService->get("mailer.host");
                $mail->Username = $configService->get("mailer.username");
                $mail->Port = $configService->get("mailer.port");
                $mail->SMTPAuth = $configService->get("mailer.smtp.auth");
                $mail->Password = $configService->get("mailer.smtp.password");
                $mail->SMTPSecure = $configService->get("mailer.smtp.secure");

                /**
                 * Add Recipients ({to,cc,bcc}Addresses)
                 */
                foreach ($options["to"] as $key => $value) {
                    $mail->addAddress($key, $value);
                }
                foreach ($options["cc"] as $key => $value) {
                    $mail->addCC($key, $value);
                }
                foreach ($options["bcc"] as $key => $value) {
                    $mail->addBCC($key, $value);
                }
                foreach ($options["replyTo"] as $key => $value) {
                    $mail->addReplyTo($key, $value);
                }

                /**
                 * Add Attachments:
                 */
                foreach ($options["attachment"] as $key => $value) {
                    $mail->addAttachment($key, $value);
                }

                /**
                 * Add Content
                 */
                $mail->isHTML($options["html"]);
                $mail->Subject = $options["subject"];
                $mail->Body = $options["body"];
                $mail->send();
                //print_r($mail);

                Oforge()->Logger()->get("mailer")->info("Message has been sent", $options);
            } catch (Exception $e) {
                Oforge()->Logger()->get("mailer")->error("Message has been sent", $mail->ErrorInfo);
            }
        }
    }

    private function isValid(array $options): bool
    {

        /**
         * Check if required keys are within the options-array
         */
        $keys = ["to", "subject", "body"];
        foreach ($keys as $key) {
            if (!array_key_exists($key, $options)) {
                throw new ConfigOptionKeyNotExists($key);
            }
        }

        /**
         * Check mail addresses TODO: Prevent Exception for empty array
         */
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
            } else {
                //Array Key does not exist
                throw new \InvalidArgumentException("Mandatory key $key doesn't exist");
            }
        }

        return true;
    }
}
