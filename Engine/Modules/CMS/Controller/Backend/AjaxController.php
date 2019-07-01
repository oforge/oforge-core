<?php

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Auth\Services\AuthService;
use Oforge\Engine\Modules\CMS\Services\CmsOrderService;
use Oforge\Engine\Modules\CMS\Services\PagesControllerService;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
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
     *
     * @return mixed
     * @throws ServiceNotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @EndpointAction()
     */
    public function orderAction(Request $request, Response $response) {
        if ($_POST && isset($_POST["data"])) {
            $data = json_decode($_POST["data"], true);

            $auth = null;
            if (isset($_SESSION['auth'])) {
                $auth = $_SESSION['auth'];
            }

            /** @var AuthService $authService */
            $authService = Oforge()->Services()->get('auth');
            $user        = $authService->decode($auth);
            $result      = false;
            if ($user != null) {
                /**
                 * @var $service CmsOrderService
                 */
                $service = Oforge()->Services()->get('cms.order');
                $result  = $service->order($data);
            }

            Oforge()->View()->assign(["json" => ["success" => $result]]);
        }
    }

    public function initPermissions() {
        $this->ensurePermissions('orderAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }

}
