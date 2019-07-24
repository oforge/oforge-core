<?php

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\CMS\Services\CmsOrderService;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PagesController
 *
 * @package Oforge\Engine\Modules\CMS\Controller\Backend
 * @EndpointClass(path="/backend/cms/ajax", name="backend_cms_ajax", assetScope="Backend")
 */
class AjaxController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function orderAction(Request $request, Response $response) {
        $result   = false;
        $postData = $request->getParsedBody();
        if ($request->isPost() && isset($postData['data'])) {
            $user = Oforge()->View()->get('user');
            if ($user !== null) {
                try {
                    /** @var CmsOrderService $service */
                    $service = Oforge()->Services()->get('cms.order');
                    $result  = $service->order($postData['data']);
                } catch (ServiceNotFoundException $exception) {
                    Oforge()->Logger()->logException($exception);
                }
            }
        }
        Oforge()->View()->assign(['json' => ['success' => $result]]);
    }

    public function initPermissions() {
        $this->ensurePermissions('orderAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
