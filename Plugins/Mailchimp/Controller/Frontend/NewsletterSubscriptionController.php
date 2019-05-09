<?php

namespace Mailchimp\Controller\Frontend;

use Interop\Container\Exception\ContainerException;
use Mailchimp\Services\MailchimpNewsletterService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NewsletterSubscriptionController
 *
 * @package Mailchimp\Controller\Frontend
 * @EndpointClass(path="/newsletter", name="frontend_newsletter_subscription", assetScope="Frontend")
 */
class NewsletterSubscriptionController extends AbstractController
{

    /**
     * @param Request $request
     * @param Response $response
     * @throws ContainerException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response)
    {
        $router = Oforge()->Container()->get('router');

        $subscribeLink = $router->pathFor('frontend_newsletter_subscription');
        Oforge()->View()->assign(['subscribeLink' => $subscribeLink]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerException
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @EndpointAction()
     */
    public function subscribeAction(Request $request, Response $response)
    {
        /** @var MailchimpNewsletterService $mailchimpNewsletterService */
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');
        $router                     = Oforge()->Container()->get('router');
        $body                       = $request->getParsedBody();
        $email                      = $body['frontend_newsletter_email'];
        $uri                        = $router->pathFor('frontend_newsletter_subscription');

        if (!$email) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        } else {
            $mailchimpNewsletterService->addListMember("$email");
            return $response->withRedirect($uri, 302);
        }
    }

    public function unsubscribeAction(Request $request, Response $response)
    {
        /** @var MailchimpNewsletterService $mailchimpNewsletterService */
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');

        $router = Oforge()->Container()->get('router');
        $body = $request->getParsedBody();
        $email = $body['frontend_newsletter_email'];
        $uri = $router->pathFor('frontend_newsletter_subscription');

        if (!$email) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        } else {
            $mailchimpNewsletterService->removeListMember("$email");
            return $response->withRedirect($uri, 302);
        }
    }
}