<?php

namespace TestMail\Controller\Frontend;

use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ShowMailController
 *
 * @package TestMail\Controller\Frontend
 * @EndpointClass(path="/showmail", name="showmail", assetScope="Frontend")
 */
class ShowMailController extends AbstractController {
    public function indexAction(Request $request, Response $response) {
        $mailservice         = Oforge()->Services()->get('mail');
        $options['template'] = $request->getQueryParam('template');
        $renderedMail        = $mailservice->renderMail($options, []);
        $response->write($renderedMail);
        return $response;
    }
}
