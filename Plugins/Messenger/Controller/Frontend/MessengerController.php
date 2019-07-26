<?php

namespace Messenger\Controller\Frontend;

use Doctrine\DBAL\Schema\View;
use Doctrine\ORM\ORMException;
use FrontendUserManagement\Abstracts\SecureFrontendController;
use FrontendUserManagement\Models\User;
use FrontendUserManagement\Services\UserService;
use Messenger\Models\Conversation;
use Messenger\Services\FrontendMessengerService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ConfigOptionKeyNotExistException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\I18n\Helper\I18N;
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
     * @throws ConfigElementNotFoundException
     * @throws ConfigOptionKeyNotExistException
     * @throws ORMException
     * @throws ServiceNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction(path="[/{id:.*}]", name="messages")
     */
    public function indexAction(Request $request, Response $response, array $args) {
        /** @var FrontendMessengerService $frontendMessengerService */ /** @var User $user */
        /** @var UserService $frontendUserService */
        $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
        $user                     = Oforge()->View()->get('current_user');

        /** @var Conversation[] $conversationList */
        $conversationList = $frontendMessengerService->getConversationList($user['id']);
        Oforge()->View()->assign(['conversationList' => $conversationList]);

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        /* Get a conversation: /messages/conversationId */
        if (isset($args) && isset($args['id'])) {
            $conversationId = $args['id'];

            /** @var Conversation $conversation */
            $frontendMessengerService->updateLastSeen($conversationId, $user['id']);
            $activeConversation = $frontendMessengerService->getConversation($conversationId, $user['id']);
            $isRequester        = ($activeConversation['requester'] == $user['id']);
            /* Check for permission of conversation */
            if (!($activeConversation['requested'] == $user['id'] || $activeConversation['requester'] == $user['id'])) {
                return $response->withRedirect("/404", 301);
            }

            /* Create a new message for a given conversation */
            if ($request->isPost()) {
                $message = $request->getParsedBody()['message'];

                /** @var FrontendMessengerService $frontendMessengerService */
                /** @var MailService $mailService */
                $frontendMessengerService = Oforge()->Services()->get('frontend.messenger');
                $mailService              = Oforge()->Services()->get('mail');

                $frontendMessengerService->sendMessage($activeConversation['id'], $user['id'], $message);

                $targetUserId = ($isRequester) ? $activeConversation['requested'] : $targetUserId = $activeConversation['requester'];

                $uri = $router->pathFor('frontend_account_messages') . DIRECTORY_SEPARATOR . $activeConversation['id'];

                /** only send mails for classified advert */
                if ($activeConversation['requesterType'] == 1 && $activeConversation['requestedType'] == 1) {
                    $lastMessage = end($activeConversation['messages']);
                    /** send mail if posted message is first message */
                    if ($lastMessage == false) {
                        $mailService->sendNewMessageInfoMail($targetUserId, $activeConversation['id']);
                    } else {
                        $lastMessageUser = $lastMessage->toArray()['sender'];
                        /** send mail if last message came from other chat user */
                        if ($lastMessageUser != $user['id']) {
                            $mailService->sendNewMessageInfoMail($targetUserId, $activeConversation['id']);
                        }
                    }
                }

                return $response->withRedirect($uri, 302);
            }

            Oforge()->View()->assign(['activeConversation' => $activeConversation]);

        } else {
            if (sizeof($conversationList) > 0) {
                /** @var Router $router */
                $router = Oforge()->App()->getContainer()->get('router');
                $uri    = $router->pathFor('frontend_account_messages') . '/' . $conversationList[0]['id'];

                return $response->withRedirect($uri, 302);
            }
        }
    }

    public function initPermissions() {
        $this->ensurePermission('indexAction');
    }
}
