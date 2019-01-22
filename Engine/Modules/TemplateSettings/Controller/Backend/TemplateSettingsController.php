<?php

namespace Oforge\Engine\Modules\TemplateSettings\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\TemplateEngine\Services\TemplateManagementService;
use Slim\Http\Request;
use Slim\Http\Response;

class TemplateSettingsController extends SecureBackendController
{
    /**
     * @param Request $request
     * @param Response $response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     * @throws \Oforge\Engine\Modules\Core\Exceptions\TemplateNotFoundException
     */
    public function indexAction(Request $request, Response $response)
    {
        /**
         * @var TemplateManagementService $templateManagementService
         */
        $templateManagementService = Oforge()->Services()->get('template.management');

        if($request->isPost()) {
            $formData = $request->getParsedBody();
            if(isset($formData['selectedTheme'])) {
                $templateManagementService->activate($formData['selectedTheme']);
            }
            $templateManagementService->build();
        }

        $data = $templateManagementService->list();

        Oforge()-> View()->assign(["templates" => $data]);
    }

    public function initPermissions()
    {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}
