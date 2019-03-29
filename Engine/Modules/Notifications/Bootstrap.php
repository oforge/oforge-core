<?php

namespace Oforge\Engine\Modules\Notifications;

use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;
use Oforge\Engine\Modules\Notifications\Controller\BackendNotificationController;
use Oforge\Engine\Modules\Notifications\Services\BackendNotificationService;

class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            '/backend/notifications/{id}' => [
                'controller'   => BackendNotificationController::class,
                'name'         => 'backend_notifications',
            ],
        ];

        $this->services = [
            'backend.notifications' => BackendNotificationService::class,
        ];

        $this->models = [
            BackendNotification::class,
        ];
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExists
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function activate() {
        /** @var $navigationService BackendNavigationService */
        $navigationService = Oforge()->Services()->get('backend.navigation');
        $navigationService->put([
            "name" => "notifications",
            "order" => 1,
            "position" => "topbar",
        ]);
    }
}
