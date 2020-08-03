<?php

namespace Oforge\Engine\Modules\Mailer\Services;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Insertion\Services\InsertionService;
use InvalidArgumentException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\Statics;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
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

/**
 * Class MailService
 *
 * @package Oforge\Engine\Modules\Mailer\Services
 */
class MailService {

    /**
     * Initialises PHP Mailer instance with specified mailer options and template data.
     * Options = [
     * 'to'         => ['user@host.de' => 'user_name', user2@host.de => 'user2_name, ...],
     * 'cc'         => [],
     * 'bcc'        => [],
     * 'replyTo'    => [],
     * 'attachment' => [],
     * 'subject'    => string,
     * 'template'   => string, // Twig template file name (with extension)
     * 'html'       => string, // Html body
     * 'body'       => string, // Raw text mail
     * ]
     * TemplateData = ['key' = value, ... ]
     *
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
                /** @var  $configService */
                $configService = Oforge()->Services()->get('config');
                $exceptions    = $configService->get('mailer_exceptions');

                /** @var PHPMailer $mail */
                $mail = new PHPMailer($exceptions);

                /**  Mailer Settings */
                $mail->isSMTP();
                $mail->setFrom($this->getSenderAddress($options['from']), $configService->get('mailer_from_name'));
                $mail->Host       = $configService->get('mailer_host');
                $mail->Username   = $configService->get('mailer_smtp_username');
                $mail->Port       = $configService->get('mailer_port');
                $mail->SMTPAuth   = $configService->get('mailer_smtp_auth');
                $mail->Password   = $configService->get('mailer_smtp_password');
                $mail->SMTPSecure = $configService->get('mailer_smtp_secure');
                $mail->Encoding   = 'base64';
                $mail->CharSet    = 'UTF-8';

                /** Add Recipients ({to,cc,bcc}Addresses) */
                $redirect = $configService->get('redirect_outgoing_mail');
                if ($redirect == true) {
                    $recipient = $configService->get('redirect_recipient');
                    if (isset($recipient) && !empty($recipient)) {
                        $mail->addAddress($recipient, 'dev');

                    } else {
                        Oforge()->Logger()->get('mailer')->error('Could not redirect message, recipient not set in mailer backend options!');
                    }
                } else {
                    foreach ($options['to'] as $key => $value) {
                        $mail->addAddress($key, $value);
                    }
                }

                if (isset($options['cc'])) {
                    foreach ($options['cc'] as $key => $value) {
                        $mail->addCC($key, $value);
                    }
                }
                if (isset($options['bcc'])) {
                    foreach ($options['bcc'] as $key => $value) {
                        $mail->addBCC($key, $value);
                    }
                }
                if (isset($options['replyTo'])) {
                    foreach ($options['replyTo'] as $key => $value) {
                        $mail->addReplyTo($key, $value);
                    }
                }

                /** Add Attachments: */
                if (isset($options['attachment'])) {
                    foreach ($options['attachment'] as $key => $value) {
                        $mail->addAttachment($key, $value);
                    }
                }
                /** Generate Base-Url for Media */
                $conversationLink = 'http://';
                if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                    $conversationLink = 'https://';
                }

                if (isset($_SERVER['HTTP_HOST'])) {
                    $conversationLink        .= $_SERVER['HTTP_HOST'];
                    $templateData['baseUrl'] = $conversationLink;
                }

                if (isset($options['template'])) {
                    /** Render HTML */
                    $renderedTemplate = $this->renderMail($options, $templateData);
                    $mail->isHTML(true);
                } elseif (isset($options['html'])) {
                    $renderedTemplate = $options['html'];
                    $mail->isHTML(true);
                } elseif (isset($options['text'])) {
                    $renderedTemplate = $options['text'];
                    $mail->isHTML(false);
                }

                /** Add Content */
                $mail->Subject = $options['subject'];
                $mail->Body    = $renderedTemplate;

                $mail->send();

                if ($redirect == true) {
                    Oforge()->Logger()->get('mailer')->info('Message has been redirected', [$mail->getToAddresses(), $options, $templateData]);
                } else {
                    Oforge()->Logger()->get('mailer')->info('Message has been sent', [$options, $templateData]);
                }

                return true;

            } catch (Exception $e) {
                Oforge()->Logger()->get('mailer')->error('Message has not been sent', [$mail->ErrorInfo]);

                return false;
            }
        }

        return false;
    }

    /**
     * Loads minimal twig environment and returns rendered HTML-template with inlined CSS from active theme.
     * If specified template does not exists in active theme -> fallback to base theme
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
        $templateManagementService = Oforge()->Services()->get('template.management');
        $templateName              = $templateManagementService->getActiveTemplate()->getName();
        $templatePath              = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $templateName . DIRECTORY_SEPARATOR . 'MailTemplates';

        if (!file_exists($templatePath . DIRECTORY_SEPARATOR . $options['template'])) {
            $templatePath = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . Statics::DEFAULT_THEME . DIRECTORY_SEPARATOR . 'MailTemplates';
        }

        $twig = new CustomTwig($templatePath, ['cache' => ROOT_PATH . DIRECTORY_SEPARATOR . Statics::CACHE_DIR . '/mailer']);
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

        /** @var string $html */
        $html = $twig->fetch($template = $options['template'], $data = $templateData);

        /** @var  $inlineCssService */
        $inlineCssService = Oforge()->Services()->get('inline.css');

        return $inlineCssService->renderInlineCss($html);
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
        $configService = Oforge()->Services()->get('config');

        $host = $configService->get('mailer_from_host');
        if (!$host) {
            throw new InvalidArgumentException('Error: Host is not set');
        }
        $sender = $configService->get('mailer_from_' . $key);

        $senderAddress = $sender . '@' . $host;

        return $senderAddress;
    }

    /**
     * @param $userId
     * @param $conversationId
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function sendNewMessageInfoMail($userId, $conversationId) {
        /** @var  UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');

        /** @var User $user */
        $user = $userService->getUserById($userId);

        $userMail      = $user->getEmail();
        $mailerOptions = [
            'to'       => [$userMail => $userMail],
            'from'     => 'no_reply',
            'subject'  => I18N::translate('mailer_subject_new_message'),
            'template' => 'NewMessage.twig',
        ];
        $templateData  = [
            'conversationId' => $conversationId,
            'receiver_name'  => $user->getDetail()->getNickName(),
            'sender_mail'    => $this->getSenderAddress('no_reply'),
        ];

        return $this->send($mailerOptions, $templateData);
    }

    /**
     * @param $insertionId
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function sendInsertionApprovedInfoMail($insertionId) {
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');

        /** @var Insertion $insertion */
        $insertion = $insertionService->getInsertionById($insertionId);

        /** @var User $user */
        $user     = $insertion->getUser();
        $userMail = $user->getEmail();

        $mailerOptions = [
            'to'       => [$userMail => $userMail],
            'from'     => 'no_reply',
            'subject'  => I18N::translate('mailer_subject_insertion_approved'),
            'template' => 'InsertionApproved.twig',
        ];
        $templateData  = [
            'insertionId'   => $insertionId,
            // TODO: add title 'insertionTitle'   => $insertion->getContent(),
            'receiver_name' => $user->getDetail()->getNickName(),
            'sender_mail'   => $this->getSenderAddress('no_reply'),
        ];

        return $this->send($mailerOptions, $templateData);
    }

    /**
     * @param $userId
     * @param $newResultsCount
     * @param $searchLink
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function sendNewSearchResultsInfoMail($userId, $newResultsCount, $searchLink) {
        /** @var  UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');

        /** @var User $user */
        $user     = $userService->getUserById($userId);
        $userMail = $user->getEmail();

        $mailerOptions = [
            'to'       => [$userMail => $userMail],
            'from'     => 'no_reply',
            'subject'  => I18N::translate('mailer_subject_new_search_results'),
            'template' => 'NewSearchResults.twig',
        ];
        $templateData  = [
            'resultCount'   => $newResultsCount,
            'searchLink'    => $searchLink,
            'sender_mail'   => $this->getSenderAddress('no_reply'),
            'receiver_name' => $user->getDetail()->getNickName(),
        ];

        return $this->send($mailerOptions, $templateData);
    }

    /**
     * @param User $user
     * @param Insertion $insertion
     *
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public function sendInsertionCreateInfoMail(User $user, Insertion $insertion) {
        $userMail      = $user->getEmail();
        $mailerOptions = [
            'to'       => [$userMail => $userMail],
            'from'     => 'no_reply',
            'subject'  => I18N::translate('mailer_subject_insertion_created', 'Insertion was created'),
            'template' => 'InsertionCreated.twig',
        ];
        $templateData  = [
            'insertionId'    => $insertion->getId(),
            'insertionTitle' => $insertion->getContent()[0]->getTitle(),
            'receiver_name'  => $user->getDetail()->getNickName(),
            'sender_mail'    => $this->getSenderAddress('no_reply'),
        ];
        $this->send($mailerOptions, $templateData);

        try {
            I18N::translate('mailer_insertion_waiting_for_moderating_btn', [
                'en'=> 'moderate',
                'de' => 'moderieren',
            ]);
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            $moderatorMail = $configService->get('insertions_creation_moderator_mail');
            $moderatorName = $configService->get('insertions_creation_moderator_name');
            if (!empty($moderatorMail)) {
                if (empty($moderatorName)) {
                    $moderatorName = $moderatorMail;
                }
                $mailerOptions = [
                    'to'       => [$moderatorMail => $moderatorName],
                    'from'     => 'no_reply',
                    'subject'  => I18N::translate('mailer_subject_insertion_waiting_for_moderating', [
                        'en' => 'A new insertion is waiting for moderation',
                        'de' => 'Ein neues Inserat wartet auf Moderation',
                    ]),
                    'template' => 'InsertionWaitingForModerating.twig',
                ];
                $templateData  = [
                    'insertionID'    => $insertion->getId(),
                    'insertionTitle' => $insertion->getContent()[0]->getTitle(),
                    'sender_mail'    => $this->getSenderAddress('no_reply'),
                ];
                $this->send($mailerOptions, $templateData);
            }
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     * @param array $options
     *
     * @return bool
     * @throws ConfigOptionKeyNotExistException
     */
    private function isValid(array $options) : bool {
        $keys = ['to', 'subject'];
        foreach ($keys as $key) {
            if (!isset($options[$key])) {
                throw new ConfigOptionKeyNotExistException($key);
            }
        }

        /** Validate Mail Addresses */
        $emailKeys = ['to', 'cc', 'bcc', 'replyTo'];
        foreach ($emailKeys as $key) {
            if (array_key_exists($key, $options)) {
                if (is_array($options[$key])) {
                    foreach ($options[$key] as $email => $name) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            Oforge()->Logger()->logException(new InvalidArgumentException("$email is not a valid email."));

                            return false;
                        }
                    }
                } else {
                    // Argument is not an Array
                    Oforge()->Logger()->logException(new InvalidArgumentException("Expected array for $key but get " . gettype($options[$key])));
                    return false;
                }
            }
        }

        return true;
    }

}
