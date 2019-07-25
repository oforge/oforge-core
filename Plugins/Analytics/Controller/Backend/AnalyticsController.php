<?php

namespace Analytics\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AnalyticsController
 *
 * @package Analytics|Controller|Backend
 * @EndpointClass(path="/backend/analytics", name="backend_analytics", assetScope="Backend")
 */
class AnalyticsController extends SecureBackendController {
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

    /**
     * @param Request $request
     * @param Response $response
     */
    public function refreshAction(Request $request, Response $response) {
        // Update Data
    }

    /** @inheritdoc */
    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'refreshAction',
        ], BackendUser::ROLE_ADMINISTRATOR);
    }

}
