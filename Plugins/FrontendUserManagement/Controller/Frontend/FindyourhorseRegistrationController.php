<?php

namespace FrontendUserManagement\Controller\Frontend;

use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FindyourhorseRegistrationController
 * @package FrontendUserManagement\Controller\Frontend
 *
 * @EndpointClass(path="/findyourhorse", name="frontend_findyourhorse", assetScope="Frontend")
 */
class FindyourhorseRegistrationController extends AbstractController {
    public function indexAction(Request $request, Response $response) {

    }
}
