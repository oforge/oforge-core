<?php

namespace FrontendUserManagement\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Services\UserAddressService;
use FrontendUserManagement\Services\UserDetailsService;
use FrontendUserManagement\Services\UserService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class UserDetailsController
 *
 * @package FrontendUserManagement\Controller\Frontend
 * @EndpointClass(path="/account/details", name="frontend_account_details", assetScope="Frontend")
 */
class UserDetailsController extends SecureFrontendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $userId = Oforge()->View()->get('current_user')['id'];
        /** @var UserService $userService */
        $userService = Oforge()->Services()->get('frontend.user.management.user');
        $user        = $userService->getUserById($userId);

        Oforge()->View()->assign([
            'userAddress' => $user->getAddress(),
            'userDetails' => $user->getDetail(),
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function process_detailsAction(Request $request, Response $response) {
        /**
         * @var Router $router
         * @var UserDetailsService $userDetailsService
         */
        $userDetailsService = Oforge()->Services()->get('frontend.user.management.user.details');
        $router             = Oforge()->App()->getContainer()->get('router');
        $body               = $request->getParsedBody();
        $token              = $body['token'];
        $firstName          = $body['frontend_account_details_first_name'];
        $lastName           = $body['frontend_account_details_last_name'];
        $nickName           = $body['frontend_account_details_nick_name'];
        $contactEmail       = $body['frontend_account_details_contact_email'];
        $contactPhone       = $body['frontend_account_details_contact_phone'];
        $userId             = Oforge()->View()->get('current_user')['id'];

        $uri = $router->pathFor('frontend_account_details');

        /**
         * no valid form data found
         */
        if (!$token || !$nickName ) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        if (!$_SESSION['auth']) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('missing_session_auth', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');

            return $response->withRedirect($uri, 302);
        }

        $userDetails = [
            'firstName'    => $firstName,
            'lastName'     => $lastName,
            'nickName'     => $nickName,
            'contactEmail' => $contactEmail,
            'phoneNumber'  => $contactPhone,
            'userId'       => $userId,
        ];

        $userDetailsSaved = $userDetailsService->save($userDetails);

        if (!$userDetailsSaved) {
            Oforge()->View()->Flash()
                    ->addMessage('warning', I18N::translate('userdetails_update_data_failed', 'Something went wrong while saving the user details.'));
            Oforge()->Logger()->get()->addError('UserDetailsController: User details could not be saved.', ['userDetails' => $userDetails]);

            return $response->withRedirect($uri, 302);
        }
        Oforge()->View()->Flash()
                ->addMessage('success', I18N::translate('userdetails_update_data_success', 'You have successfully updated your user details.'));

        return $response->withRedirect($uri, 302);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function process_addressAction(Request $request, Response $response) {
        /** @var UserAddressService $userAddressService */
        $userAddressService = Oforge()->Services()->get('frontend.user.management.user.address');
        $body               = $request->getParsedBody();
        $token              = $body['token'];
        $streetName         = $body['user_address_street_name'];
        $streetNumber       = $body['user_address_street_number'];
        $postCode           = $body['user_address_post_code'];
        $city               = $body['user_address_city'];
        $userId             = Oforge()->View()->get('current_user')['id'];

        /**
         * no valid form data found
         */
        if (!$token) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return RouteHelper::redirect($response, 'frontend_account_details');
        }

        /**
         * invalid token was sent
         */
        if (!hash_equals($_SESSION['token'], $body['token'])) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_token', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');

            return RouteHelper::redirect($response, 'frontend_account_details');
        }

        if (!$_SESSION['auth']) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('missing_session_auth', 'The data has been sent from an invalid form.'));
            Oforge()->Logger()->get()->addWarning('Someone tried to change the password without a valid form csrf token! Redirecting back to login.');

            return RouteHelper::redirect($response, 'frontend_account_details');
        }

        $userAddress = [
            'streetName'   => $streetName,
            'streetNumber' => $streetNumber,
            'postCode'     => $postCode,
            'city'         => $city,
            'userId'       => $userId,
        ];

        $userDetailsSaved = $userAddressService->save($userAddress);

        if ($userDetailsSaved) {
            Oforge()->View()->Flash()
                    ->addMessage('success', I18N::translate('useraddress_update_data_success', 'You have successfully updated your user address.'));
        } else {
            Oforge()->View()->Flash()
                    ->addMessage('warning', I18N::translate('useraddress_update_data_failed', 'Something went wrong while saving the user address.'));
            Oforge()->Logger()->get()->addError('UserDetailsController: User address could not be saved.', ['userAddress' => $userAddress]);
        }

        return RouteHelper::redirect($response, 'frontend_account_details');
    }

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'process_detailsAction',
            'process_addressAction',
        ]);
    }

}
