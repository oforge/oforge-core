<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.12.2018
 * Time: 12:52
 */

namespace Oforge\Engine\Modules\UserManagement\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\NotFoundException;
use Oforge\Engine\Modules\UserManagement\Services\BackendUsersCrudService;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;

/**
 * Class UserManagementController
 *
 * @package Oforge\Engine\Modules\UserManagement\Controller\Backend
 * @EndpointClass(path="/backend/users", name="backend_users", assetScope="Backend")
 */
class UserManagementController extends SecureBackendController {
    /** @var BackendUsersCrudService $backendUsersCrudService */
    private $backendUsersCrudService;
    /** @var Router $router */
    private $router;

    public function __construct() {
        $this->backendUsersCrudService = Oforge()->Services()->get('backend.users.crud');
        $this->router                  = Oforge()->App()->getContainer()->get('router');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $params = [];
        $users  = $this->backendUsersCrudService->list($params);
        Oforge()->View()->assign(['users' => $users]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function createAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $this->backendUsersCrudService->create($request->getParsedBody());

            return $response->withRedirect($this->router->pathFor('backend_users'), 302);
        }
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws NotFoundException
     * @EndpointAction()
     */
    public function updateAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $body = $request->getParsedBody();

            if (isset($body['password']) && $body['password'] === '') {
                unset($body['password']);
            }

            $this->backendUsersCrudService->update($body);

            return $response->withRedirect($this->router->pathFor('backend_users'), 302);
        }
        $idToUpdate = $request->getParam('id');
        $user       = $this->backendUsersCrudService->getById($idToUpdate);
        Oforge()->View()->assign(['user' => $user]);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @EndpointAction()
     */
    public function deleteAction(Request $request, Response $response) {
        if ($request->isPost()) {
            $body = $request->getParsedBody();
            if (isset($body['id'])) {
                $this->backendUsersCrudService->delete($body['id']);
            }

            return $response->withRedirect($this->router->pathFor('backend_users'), 302);
        }

        $idToDelete = $request->getParam('id');
        $user       = $this->backendUsersCrudService->getById($idToDelete);
        Oforge()->View()->assign(['user' => $user]);
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions('createAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions('updateAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
        $this->ensurePermissions('deleteAction', BackendUser::class, BackendUser::ROLE_ADMINISTRATOR);
    }

}
