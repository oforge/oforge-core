<?php

namespace Oforge\Engine\Modules\Notifications\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;
use Oforge\Engine\Modules\Notifications\Services\BackendNotificationService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BackendNotificationController
 *
 * @package Oforge\Engine\Modules\Notifications\Controller
 * @EndpointClass(path="/backend/notifications", name="backend_notifications", assetScope="Backend")
 */
class BackendNotificationController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @EndpointAction(path="/{id}")
     */
    public function indexAction(Request $request, Response $response, array $args) {
        if (isset($args['id'])) {
            /** @var BackendNotificationService $backendNotificationService */
            $backendNotificationService = Oforge()->Services()->get('backend.notifications');
            /** @var BackendNotification $notification */
            $notification = $backendNotificationService->getNotificationById($args['id']);
            if (isset($notification)) {
                $backendNotificationService->markAsSeen($notification->getId());
                $link     = $notification->getLink();
                $response = $response->withStatus(302)->withRedirect(empty($link) ? '/backend/dashboard' : $link);

                return $response;
            }
        }
        $response = $response->withStatus(404);

        return $response;
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
