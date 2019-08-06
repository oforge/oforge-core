<?php
/**
 * Created by PhpStorm.
 * User: steff
 * Date: 14.12.2018
 * Time: 15:08
 */

namespace Oforge\Engine\Modules\AdminBackend\Documentation\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DocumentationUIController
 *
 * @package Oforge\Engine\Modules\AdminBackend\Documentation\Controller\Backend
 * @EndpointClass(path="/backend/documentation/ui", name="backend_documentation_ui", assetScope="Backend")
 */
class DocumentationUIController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function generalAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function iconsAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function buttonsAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function modalsAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function slidersAction(Request $request, Response $response) {
    }

    /**
     * @param Request $request
     * @param Response $response
     * @EndpointAction()
     */
    public function timelineAction(Request $request, Response $response) {
    }

    public function initPermissions() {
        $this->ensurePermissions([
            'indexAction',
            'generalAction',
            'iconsAction',
            'buttonsAction',
            'modalsAction',
            'slidersAction',
            'timelineAction',
        ], BackendUser::ROLE_MODERATOR);
    }

}
