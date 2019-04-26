<?php

namespace Helpdesk\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use Helpdesk\Services\HelpdeskTicketService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class FrontendHelpdeskController extends SecureFrontendController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        $user = Oforge()->View()->get('user');

        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        $issueTypes = $helpdeskTicketService->getIssueTypes();
        $tickets = $helpdeskTicketService->getTicketsByOpener($user['id']);
        Oforge()->View()->assign(['issueTypes' => $issueTypes, 'tickets' => $tickets]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     */
    public function submitAction(Request $request, Response $response) {
        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');
        $uri = $router->pathFor('frontend_account_support');
        $user = Oforge()->View()->get('user');
        $body = $request->getParsedBody();
        $issueType = $body['helpdesk_request'];
        $issueTitle = $body['helpdesk_request_title'];
        $issueMessage = $body['helpdesk_request_description'];

        if (!$request->isPost()) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('no_post_method', 'No post method'));
            return $response->withRedirect($uri, 302);
        }

        if (!isset ($user)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('not_logged_in', 'Not logged in'));
            $uri = $router->pathFor('frontend_login');
            return $response->withRedirect($uri, 302);
        }

        if (!isset($issueType) || !isset($issueTitle) || !isset($issueMessage)) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('no_post_method', 'No form data'));
            return $response->withRedirect($uri, 302);
        }

        /** @var HelpdeskTicketService $helpdeskTicketService */
        $helpdeskTicketService = Oforge()->Services()->get('helpdesk.ticket');
        $helpdeskTicketService->createNewTicket($user['id'], $issueType, $issueTitle, $issueMessage);
        // TODO path for account messages
        Oforge()->View()->Flash()->addMessage('success', I18N::translate('ticket_created', 'You have successfully created a new ticket'));
        return $response->withRedirect($uri, 302);
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("submitAction", User::class);
    }
}
