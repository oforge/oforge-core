<?php

namespace Oforge\Engine\Modules\Notifications;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Services\BackendNavigationService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractBootstrap;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExistException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ParentNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Notifications\Controller\BackendNotificationController;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;
use Oforge\Engine\Modules\Notifications\Services\BackendNotificationService;

/**
 * Class Bootstrap
 *
 * @package Oforge\Engine\Modules\Notifications
 */
class Bootstrap extends AbstractBootstrap {
    /**
     * Bootstrap constructor.
     */
    public function __construct() {
        $this->endpoints = [
            BackendNotificationController::class,
        ];

        $this->models = [
            BackendNotification::class,
        ];

        $this->services = [
            'backend.notifications' => BackendNotificationService::class,
        ];
    }

    /**
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementAlreadyExistException
     * @throws ConfigOptionKeyNotExistException
     * @throws ParentNotFoundException
     */
    public function activate() {
        /** @var BackendNavigationService $navigationService */
        $navigationService = Oforge()->Services()->get('backend.navigation');
        $navigationService->put([
            'name'     => 'notifications',
            'order'    => 1,
            'position' => 'topbar',
        ]);
    }

}
