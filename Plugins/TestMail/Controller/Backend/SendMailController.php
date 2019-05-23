<?php

namespace TestMail\Controller\Backend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ShowMailController
 *
 * @package TestMail\Controller\Backend
 * @EndpointClass(path="/backend/sendmail", name="backend_sendmail", assetScope="Backend")
 */
class SendMailController extends AbstractController {
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
        ];
        try {
            $mailservice->send($testOptions, []);
        }
        catch(\Exception $e) {
            echo $e;
        }
    }
}
