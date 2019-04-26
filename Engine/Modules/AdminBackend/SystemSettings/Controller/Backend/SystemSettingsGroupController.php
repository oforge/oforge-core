<?php

namespace Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SystemSettingsGroupController
 *
 * @package Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend
 * @EndpointClass(path="/backend/settings/{group}", name="backend_settings_group", assetScope="Backend")
 */
class SystemSettingsGroupController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ConfigElementNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response, array $args) {
        try {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            if ($request->isPost()) {
                $formData = $request->getParsedBody();
                foreach ($formData as $key => $value) {
                    $configService->set($key, $value);
                }
            }
            $config = $configService->list($args['group']);
            Oforge()->View()->assign([
                'page_header' => $args['group'],
                'config'      => $config,
                'groupname'   => $args['group'],
            ]);
        } catch (ServiceNotFoundException $exception) {
            // return $response->withRedirect();TODO fix missing parameter
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

}
