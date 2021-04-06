<?php

namespace TestMail\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\Core\Manager\Events\Event;
use Oforge\Engine\Modules\Core\Models\Endpoint\EndpointMethod;
use Oforge\Engine\Modules\Mailer\Services\MailService;
use Slim\Http\Request;
use Slim\Http\Response;
use Twig_Error_Loader;
use Twig_Error_Runtime;
use Twig_Error_Syntax;

/**
 * Class BackendController
 * @EndpointClass(path="/backend/test-mail", name="backend_test_mail", assetScope="Backend")
 */
class BackendController extends SecureBackendController
{

    public function initPermissions()
    {
        $this->ensurePermissions(
            [
                'indexAction',
                'sendAction',
                'showAction',
            ],
            BackendUser::ROLE_ADMINISTRATOR
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response)
    {
        $mailIds = Oforge()->Events()->trigger(Event::create('mail.test.mailIds', [], []));
        ksort($mailIds);
        Oforge()->View()->assign(
            [
                'TestMailController' => $mailIds,
            ]
        );
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @EndpointAction(path="/send", method=EndpointMethod::POST)
     */
    public function sendAction(Request $request, Response $response, array $args) : Response
    {
        $postData = $request->getParsedBody();
        $mailId   = $postData['id'];
        /** @var MailService $mailservice */
        $mailservice = Oforge()->Services()->get('mail');
        $config      = Oforge()->Events()->trigger(Event::create('mail.test.' . $mailId, [], ['config' => [], 'data' => []]));
        $mailConfig  = $config['config'] ?? [];
        $mailData    = $config['data'] ?? [];

        $mailConfig = array_merge(
            $mailConfig,
            [
                'to'      => $postData['to'],
                'subject' => 'Test-Mail: ' . ($mailConfig['subject'] ?? $mailId),
                'from'    => $mailConfig['from'] ?? $mailservice->buildFromConfigByPrefix('info'),
            ]
        );
        Oforge()->View()->assign(
            [
                'TestMailController' => [
                    'mailConfig' => $mailConfig,
                    'mailData'   => $mailData,
                ],
            ]
        );
        $success    = $mailservice->send($mailConfig, $mailData[]);
        if ($success) {
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('mail_send_success'));
        } else {
            Oforge()->View()->Flash()->addMessage('success', I18N::translate('mail_send_failed'));
        }

        return RouteHelper::redirect($response, 'backend_test_mail');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws Twig_Error_Loader
     * @throws Twig_Error_Runtime
     * @throws Twig_Error_Syntax
     * @EndpointAction(path="/show/{id}")
     */
    public function showAction(Request $request, Response $response, array $args) : Response
    {
        $mailId = $args['id'];
        /** @var MailService $mailservice */
        $mailservice = Oforge()->Services()->get('mail');
        $config      = Oforge()->Events()->trigger(Event::create('mail.test.' . $mailId, [], ['config' => [], 'data' => []]));
        $mailConfig  = $config['config'] ?? [];
        $mailData    = $config['data'] ?? [];
        Oforge()->View()->assign(
            [
                'omitRendering'      => true,
                'TestMailController' => [
                    'id'         => $mailId,
                    'mailConfig' => $mailConfig,
                    'mailData'   => $mailData,
                ],
            ]
        );
        $rendered = $mailservice->renderTemplate($mailConfig, $mailData);

        return $response->write($rendered);
    }

}
