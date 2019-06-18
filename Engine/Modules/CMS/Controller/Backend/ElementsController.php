<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Oforge\Engine\Modules\AdminBackend\Core\Abstracts\SecureBackendController;
use Oforge\Engine\Modules\Auth\Models\User\BackendUser;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointAction;
use Oforge\Engine\Modules\Core\Annotation\Endpoint\EndpointClass;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ElementsController
 *
 * @package Oforge\Engine\Modules\CMS\Controller\Backend
 * @EndpointClass(path="/backend/types/elements", name="backend_content_elements", assetScope="Backend")
 */
class ElementsController extends SecureBackendController {

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function indexAction(Request $request, Response $response) {
        $elementsControllerService = OForge()->Services()->get("elements.controller.service");

        if (isset($_POST["cms_form"])) {
            switch ($_POST["cms_form"]) {
                case "cms_element_jstree_form":
                    switch ($_POST["cms_edit_element_action"]) {
                        case "dnd":
                            $data = $elementsControllerService->createContentElement($_POST);
                            break;
                        case "move":
                            $data = $elementsControllerService->moveElementData($_POST);
                            break;
                        default:
                            $data = $elementsControllerService->editElementData($_POST);
                            break;
                    }
                    break;
                case "cms_page_builder_form":
                    $data = $elementsControllerService->editPageBuilderData($_POST);
                    break;
                default:
                    $data = $elementsControllerService->editElementData($_POST);
                    break;
            }
        }
        // if no data is set, load the default view
        if (!isset($data)) {
            $data = $elementsControllerService->editElementData($_POST);
        }
        Oforge()->View()->assign($data);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws ServiceNotFoundException
     * @EndpointAction()
     */
    public function updateAction(Request $request, Response $response) {
        $elementsControllerService = OForge()->Services()->get("elements.controller.service");
        $data                      = [];

        if (isset($_POST["cms_form"])) {
            switch ($_POST["cms_form"]) {
                case "cms_page_builder_form":
                    $data = $elementsControllerService->editPageBuilderData($_POST);
                    break;
                default:
                    $data = $elementsControllerService->editElementData($_POST);
                    break;
            }
        }

        if (!empty($data)) {
            $data["result"] = "success";
        }

        if ($data != null) {
            Oforge()->View()->assign($data);
        }
    }

    public function initPermissions() {
        $this->ensurePermissions('indexAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
        $this->ensurePermissions('updateAction', BackendUser::class, BackendUser::ROLE_MODERATOR);
    }
}
