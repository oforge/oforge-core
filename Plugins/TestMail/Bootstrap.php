<?php

namespace TestMail;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\I18n\Helper\I18N;

/**
 * Class Bootstrap
 *
 * @package TestMail
 */
class Bootstrap extends AbstractBootstrap
{

    public function __construct()
    {
        $this->endpoints = [
            Controller\BackendController::class,
        ];
    }

    public function activate()
    {
        I18N::translate('backend_test_mail', ['en' => 'Test mail', 'de' => 'Test Mail']);
        $backendNavigationService = Oforge()->Services()->get('backend.navigation');
        $backendNavigationService->add(BackendNavigationService::CONFIG_CONTENT);
        $backendNavigationService->add(
            [
                'name'     => 'backend_test_mail',
                'order'    => 5,
                'parent'   => BackendNavigationService::KEY_CONTENT,
                'icon'     => 'fa fa-envelope',
                'path'     => 'backend_test_mail',
                'position' => 'sidebar',
            ]
        );
    }

    public function load()
    {
        //TODO move to plugins/mailer module
        $mailIds = [
            'AutoDeactivationConfirm'       => 'AutoDeactivationConfirm',
            'DeactivationConfirm'           => 'DeactivationConfirm',
            'InsertionApproved'             => 'InsertionApproved',
            'InsertionCreated'              => 'InsertionCreated',
            'InsertionWaitingForModerating' => 'InsertionWaitingForModerating',
            'NewMessage'                    => 'NewMessage',
            'NewSearchResults'              => 'NewSearchResults',
            'RegisterConfirm'               => 'RegisterConfirm',
            'RegistrationGiftCertificate'   => 'RegistrationGiftCertificate',
            'Reminder3Days'                 => 'Reminder3Days',
            'Reminder14Days'                => 'Reminder14Days',
            'Reminder30Days'                => 'Reminder30Days',
            'ResetPassword'                 => 'ResetPassword',
        ];
        Oforge()->Events()->attach(
            'mail.test.mailIds',
            Event::SYNC,
            function (Event $event) use ($mailIds) {
                $eventMailIds = $event->getReturnValue();

                $eventMailIds = array_merge($eventMailIds, $mailIds);

                $event->setReturnValue($eventMailIds);
            }
        );
        foreach ($mailIds as $mailId => $mailDescription) {
            Oforge()->Events()->attach(
                'mail.test.' . $mailId,
                Event::SYNC,
                function (Event $event) use ($mailId) {
                    $mail = [
                        'config' => [
                            'template' => $mailId . '.twig',
                        ],
                        'data'   => [], //TODO template based test data
                    ];
                    $event->setReturnValue($mail);
                }
            );
        }
    }

}
