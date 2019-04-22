<?php

namespace Helpdesk\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class BackendHelpdeskController
 *
 * @package Helpdesk\Controller\Backend
 * @EndpointClass(path="/backend/helpdesk", name="backend_helpdesk", assetScope="Backend")
 */
class BackendHelpdeskController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

        $ticketData = $helpdeskTicketService->getTickets();
        Oforge()->View()->assign(['tickets' => $ticketData]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function closeTicketAction(Request $request, Response $response) {
        if ($request->isPost()) {
            /** @var HelpdeskTicketService $helpdeskTicketService */
            $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');

            $ticketId = $request->getParsedBody()['ticketId'];

            $helpdeskTicketService->changeStatus($ticketId, 'closed');

            return $response->withRedirect('/backend/helpdesk');
        }
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
