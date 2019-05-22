<?php

namespace Mailchimp\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserDetailsService;
use Interop\Container\Exception\ContainerException;
use Mailchimp\Services\MailchimpNewsletterService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;

/**
 * Class AccountNewsletterController
 *
 * @package Mailchimp\Controller\Frontend
 * @EndpointClass(path="/account/newsletter", name="frontend_account_newsletter", assetScope="Frontend")
 */
class AccountNewsletterController extends SecureFrontendController
{
    /**
     * @param Request $request
     * @param Response $response
     * @throws ContainerException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response)
    {

        /** @var UserDetailsService $userDetailsService */
        $userDetailsService         = Oforge()->Services()->get('frontend.user.management.user.details');
        $router                     = Oforge()->Container()->get('router');
        $email                      = Oforge()->View()->get('user')['email'];
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');
        $userId                     = Oforge()->View()->get('user')['id'];
        $userDetails                = $userDetailsService->get($userId);
        $isSubscribed               = $mailchimpNewsletterService->isSubscribed($userId);
        $accountSubscribeLink       = $router->pathFor('frontend_account_newsletter');

        if ($isSubscribed)
            $subscribeMessage = I18N::translate('unsubscribe_message', 'Du bist derzeit mit der E-Mail Adresse {email} beim Newsletter angemeldet. Beim Newsletter abmelden?');
        else {
            $subscribeMessage = I18N::translate('subscribe_message', 'Du bist derzeit nicht beim Newsletter angemeldet. Mit {email} beim Newsletter anmelden?');
        }


        $subscribeMessage = str_replace('{email}', $email, $subscribeMessage);
        Oforge()->View()->assign(['isSubscribed' => $isSubscribed]);
        Oforge()->View()->assign(['subscribeMessage' => $subscribeMessage]);
        Oforge()->View()->assign(['subscribeLink' => $accountSubscribeLink]);
        if ($userDetails) {
            Oforge()->View()->assign(['userDetails' => $userDetails]);
        }



    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function subscribeAction(Request $request, Response $response)
    {
        $user = Oforge()->View()->get('user');
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');
        $router                     = Oforge()->Container()->get('router');
        $email                      = $user['email'];
        $userId                     = $user['id'];
        $uri                        = $router->pathFor('frontend_account_newsletter');


        if (!$email) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        } else {
            $mailchimpNewsletterService->addListMember("$email", $userId);
            return $response->withRedirect($uri, 302);
        }

    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerException
     * @throws ServiceNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @EndpointAction()
     */
    public function unsubscribeAction(Request $request, Response $response)
    {
        $user = Oforge()->View()->get('user');
        /** @var MailchimpNewsletterService $mailchimpNewsletterService */
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');
        $router                     = Oforge()->Container()->get('router');
        $body                       = $request->getParsedBody();
        $email                      = $body['frontend_newsletter_email'];
        $userId                     = $user['id'];
        $uri                        = $router->pathFor('frontend_account_newsletter');

        if (!$email) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        } else {

            $mailchimpNewsletterService->removeListMember("$email", $userId);
            return $response->withRedirect($uri, 302);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ContainerException
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */

    public function initPermissions()
    {
        $this->ensurePermissions('indexAction', User::class);
        $this->ensurePermissions('subscribeAction', User::class);
        $this->ensurePermissions('unsubscribeAction', User::class);
    }
}