<?php

namespace Oforge\Engine\Modules\AdminBackend\TemplateSettings\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Oforge\Engine\Modules\Core\Exceptions\Template\TemplateNotFoundException;
use Oforge\Engine\Modules\TemplateEngine\Core\Exceptions\InvalidScssVariableException;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\ScssVariableService;
use Oforge\Engine\Modules\TemplateEngine\Core\Services\TemplateManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class TemplateSettingsController
 *
 * @package Oforge\Engine\Modules\AdminBackend\TemplateSettings\Controller\Backend
 * @EndpointClass(path="/backend/templates[/]", name="backend_template_settings", assetScope="Backend")
 */
class TemplateSettingsController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws NotFoundException
     * @throws ServiceNotFoundException
     * @throws TemplateNotFoundException
     * @throws InvalidScssVariableException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        /** @var TemplateManagementService $templateManagementService */
        $templateManagementService = Oforge()->Services()->get('template.management');
        /** @var ScssVariableService $scssVariableService */
        $scssVariableService = Oforge()->Services()->get('scss.variables');

        if ($request->isPost()) {
            $formData = $request->getParsedBody();
            if (isset($formData['selectedTheme'])) {
                $templateManagementService->activate($formData['selectedTheme']);
            }

            foreach ($formData as $key => $value) {
                if (strpos($key, '|') !== false) {
                    $arr = explode('|', $key);
                    $scssVariableService->update($arr[0], $value);
                }
            }

            $templateManagementService->build();
        }
        $scssData     = $scssVariableService->getScope('Frontend');
        $templateData = $templateManagementService->list();
        Oforge()->View()->assign([
            'scssVariables' => $scssData,
            'templates'     => $templateData,
        ]);
    }

    public function initPermissions() {
        $this->ensurePermission('indexAction', BackendUser::ROLE_ADMINISTRATOR);
    }

}
