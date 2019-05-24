<?php
namespace Analytics\Controller\Backend;


use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;

/**
 * Class AnalyticsController
 *
 * @package Analytics|Controller|Backend
 * @EndpointClass(path="/backend/analytics", name="backend_analytics", assetScope="Backend")
 */

class AnalyticsController extends AbstractController {
    /**
     * @param Request $request
     * @param Response $response
     */
    public function indexAction(Request $request, Response $response) {

    }
}