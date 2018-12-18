<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 12:52
 */
namespace Oforge\Engine\Modules\UserManagement\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

class UserManagementController extends SecureBackendController {
    /**
     * @var $backendUsersCrudService BackendUsersCrudService
     */
    private $backendUsersCrudService;
    /**
     * @var $router Router
     */
    private $router;
    
    public function __construct() {
        $this->backendUsersCrudService = Oforge()->Services()->get("backend.users.crud");
        $this->router = Oforge()->App()->getContainer()->get("router");
    }
    
    public function indexAction(Request $request, Response $response) {
        $params = [];
        $users = $this->backendUsersCrudService->list($params);
        Oforge()->View()->assign(["users" => $users]);
    }
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ConfigElementAlreadyExists
     */
    public function createAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $this->backendUsersCrudService->create($request->getParsedBody());
            return $response->withRedirect($this->router->pathFor("backend_user"), 302);
        }
    }
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\NotFoundException
     */
    public function updateAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $this->backendUsersCrudService->update($request->getParsedBody());
            return $response->withRedirect($this->router->pathFor("backend_user"), 302);
        }
    }
    
    public function deleteAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $body = $request->getParsedBody();
            if (isset($body["id"])) {
                $this->backendUsersCrudService->delete($body["id"]);
            }
            return $response->withRedirect($this->router->pathFor("backend_user"), 302);
        }
        $idToDelete = $request->getParam("id");
        Oforge()->View()->assign(["userId" => $idToDelete]);
    }
    
    public function initPermissions() {
        $this->ensurePermissions("indexAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions("createAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions("updateAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions("deleteAction", BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }
}