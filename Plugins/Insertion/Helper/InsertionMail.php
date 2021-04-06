<?php

namespace Insertion\Helper;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Insertion\Models\Insertion;
use Insertion\Services\InsertionService;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class InsertionMail
 */
class InsertionMail
{

    /** prevent instance */
    private function __construct()
    {
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
    public static function sendInsertionCreateMail(User $user, Insertion $insertion)
    {
        $mailService  = self::MailService();
        $userMail     = $user->getEmail();
        $detail       = $user->getDetail();
        $nickName     = $detail === null ? $userMail : $detail->getNickName();
        $mailConfig   = [
            'to'       => [$userMail => $nickName],
            'from'     => $mailService->buildFromConfigByPrefix('no_reply'),
            'subject'  => I18N::translate('mailer_subject_insertion_created', 'Insertion was created'),
            'template' => 'InsertionCreated.twig',
        ];
        $templateData = [
            'insertionId'    => $insertion->getId(),
            'insertionTitle' => $insertion->getContent()[0]->getTitle(),
            'receiver_name'  => $nickName,
            'sender_mail'    => $mailService->buildFromMailByPrefix('no_reply'),
        ];
        $mailService->send($mailConfig, $templateData);

        try {
            I18N::translate(
                'mailer_insertion_waiting_for_moderating_btn',
                [
                    'en' => 'moderate',
                    'de' => 'moderieren',
                ]
            );
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            $moderatorMail = $configService->get('insertions_creation_moderator_mail');
            $moderatorName = $configService->get('insertions_creation_moderator_name');
            if ( !empty($moderatorMail)) {
                if (empty($moderatorName)) {
                    $moderatorName = $moderatorMail;
                }
                $mailConfig   = [
                    'to'       => [$moderatorMail => $moderatorName],
                    'from'     => $mailService->buildFromConfigByPrefix('no_reply'),
                    'subject'  => I18N::translate(
                        'mailer_subject_insertion_waiting_for_moderating',
                        [
                            'en' => 'A new insertion is waiting for moderation',
                            'de' => 'Ein neues Inserat wartet auf Moderation',
                        ]
                    ),
                    'template' => 'InsertionWaitingForModerating.twig',
                ];
                $templateData = [
                    'insertionID'    => $insertion->getId(),
                    'insertionTitle' => $insertion->getContent()[0]->getTitle(),
                    'sender_mail'    => $mailService->buildFromMailByPrefix('no_reply'),
                ];
                $mailService->send($mailConfig, $templateData);
            }
        } catch (\Exception $exception) {
            Oforge()->Logger()->logException($exception);
        }
    }

    /**
     * @param $insertionId
     *
     * @return bool
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public static function sendInsertionApprovedMail($insertionId) : bool
    {
        $mailService = self::MailService();
        /** @var InsertionService $insertionService */
        $insertionService = Oforge()->Services()->get('insertion');
        /** @var Insertion|null $insertion */
        $insertion = $insertionService->getInsertionById($insertionId);

        /** @var User $user */
        $user     = $insertion->getUser();
        $userMail = $user->getEmail();
        $detail   = $user->getDetail();
        $nickName = $detail === null ? $userMail : $detail->getNickName();

        $mailConfig   = [
            'to'       => [$userMail => $nickName],
            'from'     => $mailService->buildFromConfigByPrefix('no_reply'),
            'subject'  => I18N::translate('mailer_subject_insertion_approved'),
            'template' => 'InsertionApproved.twig',
        ];
        $templateData = [
            'insertionId'   => $insertionId,
            // TODO: add title 'insertionTitle'   => $insertion->getContent()[0]->getTitle(),
            'receiver_name' => $nickName,
            'sender_mail'   => $mailService->buildFromMailByPrefix('no_reply'),
        ];

        return $mailService->send($mailConfig, $templateData);
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
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     */
    public static function sendNewSearchResultsMail($userId, $newResultsCount, $searchLink) : bool
    {
        $mailService = self::MailService();
        /** @var  UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');
        /** @var User|null $user */
        $user     = $userService->getUserById($userId);
        $userMail = $user->getEmail();
        $detail   = $user->getDetail();
        $nickName = $detail === null ? $userMail : $detail->getNickName();

        $mailConfig   = [
            'to'       => [$userMail => $nickName],
            'from'     => $mailService->buildFromConfigByPrefix('no_reply'),
            'subject'  => I18N::translate('mailer_subject_new_search_results'),
            'template' => 'NewSearchResults.twig',
        ];
        $templateData = [
            'resultCount'   => $newResultsCount,
            'searchLink'    => $searchLink,
            'sender_mail'   => $mailService->buildFromMailByPrefix('no_reply'),
            'receiver_name' => $nickName,
        ];

        return $mailService->send($mailConfig, $templateData);
    }

    private static function MailService() : MailService
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return Oforge()->Services()->get('mail');
    }

}
