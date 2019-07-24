<?php

namespace Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend;

use Doctrine\ORM\ORMException;
use Exception;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Helper\ArrayHelper;
use Oforge\Engine\Modules\Core\Helper\RouteHelper;
use Oforge\Engine\Modules\Core\Models\Config\Config;
use Oforge\Engine\Modules\Core\Services\ConfigService;
use Oforge\Engine\Modules\I18n\Helper\I18N;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SystemSettingsController
 *
 * @package Oforge\Engine\Modules\AdminBackend\SystemSettings\Controller\Backend
 * @EndpointClass(path="/backend/settings", name="backend_settings", assetScope="Backend")
 */
class SystemSettingsController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @EndpointAction(path="[/]")
     */
    public function indexAction(Request $request, Response $response) {
        /** @var ConfigService $configService */
        $configService = Oforge()->Services()->get('config');
        $configGroups  = $configService->getConfigGroups();

        Oforge()->View()->assign(['config' => $configGroups]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     *
     * @return Response
     * @throws ORMException
     * @EndpointAction(path="/{group}", name="group")
     */
    public function groupIndexAction(Request $request, Response $response, array $args) {
        try {
            /** @var ConfigService $configService */
            $configService = Oforge()->Services()->get('config');
            if ($request->isPost()) {
                $postData = $request->getParsedBody();
                try {
                    foreach ($postData as $key => $value) {
                        $configService->update($key, $value);
                    }
                    Oforge()->View()->Flash()->addMessage('success', I18N::translate('backend_settings_update_success', 'Settings successfully updated.'));
                } catch (Exception $exception) {
                    Oforge()->View()->Flash()
                            ->addExceptionMessage('error', I18N::translate('backend_settings_update_failed', 'Update of settings failed.'), $exception);
                    Oforge()->View()->Flash()->setData(self::class, $postData);
                }

                return RouteHelper::redirect($response, null, ['group' => $args['group']]);
            }
            $groupConfigs = $configService->getGroupConfigs($args['group']);
            $groupConfigs = array_map(function ($config) {
                /** @var Config $config */
                return $config->toArray();
            }, $groupConfigs);

            if (Oforge()->View()->Flash()->hasData(self::class)) {
                $postData     = Oforge()->View()->Flash()->getData(self::class);
                $groupConfigs = ArrayHelper::mergeRecursive($groupConfigs, $postData);
                Oforge()->View()->Flash()->clearData(self::class);
            }
            Oforge()->View()->assign([
                'page_header' => $args['group'],
                'config'      => $groupConfigs,
                'groupname'   => $args['group'],
            ]);
        } catch (ServiceNotFoundException $exception) {
            // return $response->withRedirect();TODO fix missing parameter
        }
    }

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'groupIndexAction',
        ], BackendUser::ROLE_ADMINISTRATOR);
    }

}
