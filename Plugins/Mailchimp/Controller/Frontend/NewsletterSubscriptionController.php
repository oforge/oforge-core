<?php

namespace Mailchimp\Controller\Frontend;

use Mailchimp\Services\MailchimpNewsletterService;
use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NewsletterSubscriptionController
 *
 * @package Mailchimp\Controller\Frontend
 */
class NewsletterSubscriptionController extends AbstractController
{
    public function indexAction(Request $request, Response $response)
    {
        // show the email form for requesting a subscribing to the newsletter
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function subscribeAction(Request $request, Response $response)
    {
        /** @var MailchimpNewsletterService $mailchimpNewsletterService */
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');

        $router = Oforge()->Container()->get('router');
        $body = $request->getParsedBody();
        $email = $body['frontend_newsletter_email'];
        $uri = $router->pathFor('frontend_newsletter_subscription');

        if (!$email) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid form data.');
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
            Oforge()->View()->addFlashMessage('warning', 'Invalid form data.');

            return $response->withRedirect($uri, 302);
        } else {
            $mailchimpNewsletterService->removeListMember("$email");
            return $response->withRedirect($uri, 302);
        }
    }
}