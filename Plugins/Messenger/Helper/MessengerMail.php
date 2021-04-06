<?php

namespace Messenger\Helper;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class MessengerMail
 */
class MessengerMail
{

    /** prevent instance */
    private function __construct()
    {
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
     * @throws TemplateNotFoundException
     */
    public static function sendNewMessageMail($userId, $conversationId) : bool
    {
        $mailService = self::MailService();
        /** @var  UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');

        /** @var User|null $user */
        $user = $userService->getUserById($userId);

        $userMail     = $user->getEmail();
        $detail       = $user->getDetail();
        $nickName     = $detail === null ? $userMail : $detail->getNickName();
        $mailConfig   = [
            'to'       => [$userMail => $nickName],
            'from'     => $mailService->buildFromConfigByPrefix('no_reply'),
            'subject'  => I18N::translate('mailer_subject_new_message'),
            'template' => 'NewMessage.twig',
        ];
        $templateData = [
            'conversationId' => $conversationId,
            'receiver_name'  => $nickName,
            'sender_mail'    => $mailService->buildFromMailByPrefix('no_reply'),
        ];

        return $mailService->send($mailConfig, $templateData);
    }

    private static function MailService() : MailService
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Oforge()->Services()->get('mail');
    }

}
