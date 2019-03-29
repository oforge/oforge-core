<?php

namespace FrontendUserManagement\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserDetailsService;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Session\Services\SessionManagementService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class UserDetailsController extends SecureFrontendController {
    public function indexAction(Request $request, Response $response) {
        $userId = Oforge()->View()->get('user')['id'];
        /** @var UserDetailsService $userDetailsService */
        $userDetailsService = Oforge()->Services()->get('frontend.user.management.user.details');
        $userDetails = $userDetailsService->get($userId);
        if ($userDetails) {
            Oforge()->View()->assign(['userDetails' => $userDetails]);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ServiceNotFoundException
     */
    public function process_detailsAction(Request $request, Response $response) {
        /** @var Router $router */
        /** @var UserDetailsService $userDetailsService */
        $userDetailsService         = Oforge()->Services()->get('frontend.user.management.user.details');
        $router                     = Oforge()->App()->getContainer()->get('router');
        $body                       = $request->getParsedBody();
        $token                      = $body['token'];
        $firstName                  = $body['frontend_account_details_first_name'];
        $lastName                   = $body['frontend_account_details_last_name'];
        $nickName                   = $body['frontend_account_details_nick_name'];
        $contactEmail               = $body['frontend_account_details_contact_email'];
        $contactPhone               = $body['frontend_account_details_contact_phone'];
        $userId                     = Oforge()->View()->get('user')['id'];

        $uri                        = $router->pathFor('frontend_account_details');


        /**
         * no valid form data found
         */
        if (!$token||!$firstName||!$lastName||!$nickName||!$contactEmail||!$contactPhone) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid form data.');
            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->addFlashMessage('warning', 'The data has been sent from an invalid form.');
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        if (!$_SESSION['auth']) {
            Oforge()->View()->addFlashMessage('warning', 'No JsonWebToken.');
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');
            return $response->withRedirect($uri, 302);
        }

        $userDetails = [
            'firstName'     => $firstName,
            'lastName'      => $lastName,
            'nickName'      => $nickName,
            'contactEmail'  => $contactEmail,
            'contactPhone'  => $contactPhone,
            'userId'        => $userId
        ];

        $userDetailsSaved = $userDetailsService->save($userDetails);
        $uri = $router->pathFor('frontend_account_details');

        if (!$userDetailsSaved) {
            Oforge()->View()->addFlashMessage('warning', 'Something went wrong while saving the user details.');
            Oforge()->Logger()->get()->addError('UserDetailsController: User details could not be saved.', ['userDetails' => $userDetails]);
            return $response->withRedirect($uri, 302);
        }

        Oforge()->View()->addFlashMessage('success', 'You have successfully updated your user details.');
        return $response->withRedirect($uri, 302);
    }
    public function process_addressAction(Request $request, Response $response) {

    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
        $this->ensurePermissions("process_detailsAction", User::class);
        $this->ensurePermissions("process_addressAction", User::class);
    }
}