<?php

namespace TestMail\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class ShowMailController
 *
 * @package TestMail\Controller\Backend
 * @EndpointClass(path="/backend/sendmail", name="backend_sendmail", assetScope="Backend")
 */
class SendMailController extends SecureBackendController {
    public function indexAction(Request $request, Response $response) {
        $mailservice = Oforge()->Services()->get('mail');
        $testOptions = [
            'to'         => [$request->getQueryParam('to') => $request->getQueryParam('to')],
            'cc'         => [],
            'bcc'        => [],
            'replyTo'    => [],
            'subject'    => $request->getQueryParam('template'),
            'attachment' => [],
            'template'   => $request->getQueryParam('template'),
            'from'       => 'info',
        ];

        $mailservice->send($testOptions, []);
        Oforge()->View()->Flash()->addMessage('success', I18N::translate( 'mail_send_success'));

        /** @var Router $router */
        $router = Oforge()->App()->getContainer()->get('router');

        $url    = $router->pathFor('backend_testmail');

        return $response->withRedirect($url, 301);
    }
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}
