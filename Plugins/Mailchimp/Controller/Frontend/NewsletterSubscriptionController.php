<?php

namespace Mailchimp\Controller\Frontend;

use Mailchimp\Services\MailchimpNewsletterService;
use \Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Mailer\Services\MailService;
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
        // show the email form for requesting a reset link
    }

    public function subscribeAction(Request $request, Response $response) {
        /** @var MailchimpNewsletterService $mailchimpNewsletterService */
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');
        $router = Oforge()->Container()->get('router');
        $body = $request->getParsedBody();
        $email = $body['email'];
        $uri = $router->pathFor('frontend_newsletter_subscription');

        if (!$email) {
            Oforge()->View()->addFlashMessage('warning', 'Invalid form data.');

            return $response->withRedirect($uri, 302);
        } else {
            $mailchimpNewsletterService->addListMember($email);
        }
    }
}