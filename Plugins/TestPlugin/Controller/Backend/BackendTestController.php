<?php

namespace TestPlugin\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;
use TestPlugin\Services\TestService;

/**
 * Class BackendTestController
 *
 * @package Test\Controller\Backend
 * @EndpointClass(path="/backend/test", name="backend_test", assetScope="Backend")
 */
class BackendTestController extends SecureBackendController
{
    /**
     * @param Request $request
     * @param Response $response
     */
    public function indexAction(Request $request, Response $response)
    {
        /** @var TestService $testService */
        $testService    = Oforge()->Services()->get('testplugin.testservice');
        $data = $testService->getAll();

        Oforge()->View()->assign([
            'daten'   => $data
        ]);
    }

    public function initPermissions()
    {
        $this->ensurePermissions([
            'indexAction',
            'deleteAction',
            'editAction',
            'addAction'
        ], BackendUser::ROLE_MODERATOR);
    }
}
