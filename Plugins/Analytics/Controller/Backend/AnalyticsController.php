<?php
namespace Analytics\Controller\Backend;


use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;

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
     *
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /** @var  $dataService */
        $dataService = Oforge()->Services()->get('analytics.data');

        Oforge()->View()->assign(['analyticsData' => $dataService->getData()]);
    }

    public function refreshAction(Request $request, Response $response) {
        // Update Data
    }
}