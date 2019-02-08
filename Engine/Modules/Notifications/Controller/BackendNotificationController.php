<?php

namespace Oforge\Engine\Modules\Notifications\Services;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;
use Slim\Http\Request;
use Slim\Http\Response;

class BackendNotificationController extends SecureBackendController {
    
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response, $args) {
        if (isset($args['id'])) {
            /** @var BackendNotificationService $backendNotificationService */
            $backendNotificationService = Oforge()->Services()->get('backend.notifications');
            /** @var BackendNotification $notification */
            $notification = $backendNotificationService->getNotificationById($args['id']);
            if (!is_null($notification)) {
                $backendNotificationService->markAsSeen($notification->getId());
                $response = $response->withStatus(302);
                $link     = $notification->getLink();
                if ($link == '') {
                    $response = $response->withRedirect('/backend/dashboard');
                } else {
                    $response = $response->withRedirect($link);
                }
                return $response;
            }
        }
        $response = $response->withStatus(404);

        return $response;
    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}