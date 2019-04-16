<?php

namespace Mailchimp\Controller\Frontend;

use Interop\Container\Exception\ContainerException;
use Mailchimp\Services\MailchimpNewsletterService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class NewsletterSubscriptionController
 *
 * @package Mailchimp\Controller\Frontend
 */
class NewsletterSubscriptionController extends AbstractController {

    /**
     * @param Request $request
     * @param Response $response
     */
    public function indexAction(Request $request, Response $response) {
        // show the email form for requesting a reset link
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ContainerException
     * @throws ServiceNotFoundException
     */
    public function subscribeAction(Request $request, Response $response) {
        /** @var MailchimpNewsletterService $mailchimpNewsletterService */
        $mailchimpNewsletterService = Oforge()->Services()->get('mailchimp.newsletter');
        $router                     = Oforge()->Container()->get('router');
        $body                       = $request->getParsedBody();
        $email                      = $body['email'];
        $uri                        = $router->pathFor('frontend_newsletter_subscription');

        if (!$email) {
            Oforge()->View()->Flash()->addMessage('warning', I18N::translate('form_invalid_data', 'Invalid form data.'));

            return $response->withRedirect($uri, 302);
        } else {
            $mailchimpNewsletterService->addListMember($email);
        }
    }

}
