<?php

namespace Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Slim\Http\Request;
use Slim\Http\Response;

class SystemSettingsGroupController extends SecureBackendController {
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementNotFoundException
     */
    public function indexAction(Request $request, Response $response, $args) {
        try {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get("config");
            if ($request->isPost()) {
                $formData = $request->getParsedBody();
                foreach ($formData as $key => $value) {
                    $configService->set($key, $value);
                }
            }
            $config = $configService->list($args['group']);
            Oforge()->View()->assign(["page_header" => $args['group'], "config" => $config, "groupname" => $args['group']]);
        } catch (ServiceNotFoundException $exception) {
            $response->withRedirect();
        }
    }

    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}