<?php

namespace Oforge\Engine\Modules\Notifications\Services;


use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Notifications\Models\BackendNotification;
use Slim\Http\Request;
use Slim\Http\Response;

class BackendNotificationController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @return Response
     */
    public function indexAction(Request $request, Response $response, $args) {
        if(isset($args['id'])) {
            try {
                /** @var BackendNotificationService $backendNotificationService */
                $backendNotificationService = Oforge()->Services()->get('backend.notifications');
                /** @var BackendNotification $notification */
                $notification = $backendNotificationService->getNotificationById($args['id']);

                $backendNotificationService->markAsSeen($notification->getId());
                $response = $response->withStatus(302);
                $response = $response->withRedirect($notification->getLink());
                return $response;
            } catch (ServiceNotFoundException $e) {
            } catch (OptimisticLockException $e) {
            } catch (ORMException $e) { }
        }
        $response = $response->withStatus(500);
        return $response;
    }

}