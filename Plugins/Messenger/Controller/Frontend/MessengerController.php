<?php

namespace Messenger\Controller\Frontend;

use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserDetailsService;
use FrontendUserManagement\Services\UserService;
use Helpdesk\Services\HelpdeskMessengerService;
use Messenger\Models\Conversation;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Oforge\Engine\Modules\I18n\Services\InternationalizationService;
use Oforge\Engine\Modules\I18n\Services\LanguageService;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class MessengerController
 *
 * @package Messenger\Controller\Frontend
 * @EndpointClass(path="/account/messages", name="frontend_account", assetScope="Frontend")
 */
class MessengerController extends SecureFrontendController {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction(path="[/{id:.*}]", name="messages")
     */
    public function indexAction(Request $request, Response $response, array $args) {
        /** @var FrontendMessengerService $frontendMessengerService */
        /** @var User $user */
        $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
        $user = Oforge()->View()->get('user');

        /** @var Conversation[] $conversationList */
        $conversationList = $frontendMessengerService->getConversationList($user['id']);
        Oforge()->View()->assign(['conversationList' => $conversationList]);

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            /* Create a new message for a given conversation */

            if ($request->isPost()) {
                $conversationId = $args['id'];

                $senderId = $request->getParsedBody()['sender'];
                $message  = $request->getParsedBody()['message'];

                /** @var FrontendMessengerService $frontendMessengerService */
                /** @var MailService $mailService */
                /** @var UserService $frontendUserService */
                $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
                $mailService = Oforge()->Services()->get('mail');
                $frontendUserService = Oforge()->Services()->get('frontend.user.management.user');

                /** @var Conversation $conversation */
                $conversation = $frontendMessengerService->getConversation($conversationId, $senderId);
                $frontendMessengerService->sendMessage($conversation['id'], $senderId, $message);

                /* Only send mails for classified advert */
                if($conversation['type'] === 'classified_advert' && end($conversation['messages'])['sender'] != $user['id']) {
                    if ($conversation['requested'] == $user['id']) {
                        $targetUserId = $conversation['requester'];
                    } else {
                        $targetUserId = $conversation['requested'];
                    }

                    $targetIdMail = $frontendUserService->getUserById($targetUserId)->getEmail();

                    $mailOptions = [
                        'to' => [$targetIdMail => $targetIdMail],
                        'from' => 'no_reply',
                        'subject' => I18N::translate('email_subject_new_message', 'New private message'),
                        'template' => 'NewMessage.twig'
                    ];
                    $mailService->send($mailOptions);
                }
                $uri = $router->pathFor('frontend_account_messages');

                return $response->withRedirect($uri . '/' . $conversation['id'], 302);
            }

            $conversation = $frontendMessengerService->getConversation($args['id'], $user['id']);
            Oforge()->View()->assign(['conversation' => $conversation]);
        } else {
            if (sizeof($conversationList) > 0) {
                /** @var Router $router */
                $router = Oforge()->App()->getContainer()->get('router');
                $uri = $router->pathFor('frontend_account_messages') . '/' . $conversationList[0]['id'];
                return $response->withRedirect($uri, 302);
            }
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions("indexAction", User::class);
    }
}
