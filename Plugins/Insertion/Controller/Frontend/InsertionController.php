<?php

namespace Insertion\Controller\Frontend;

use FrontendUserManagement\Abstracts\SecureFrontendController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class MessengerController
 *
 * @package Messenger\Controller\Frontend
 * @EndpointClass(path="/insertions", name="insertions", assetScope="Frontend")
 */
class InsertionController extends SecureFrontendController {

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function viewAction(Request $request, Response $response) {
    }
}
