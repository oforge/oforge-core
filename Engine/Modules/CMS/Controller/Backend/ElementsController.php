<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

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
class ElementsController extends AbstractController {

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

        $data = $elementsControllerService->getElementData($_POST);
        
        switch ($_POST["cms_form"])
        {
            case "cms_element_jstree_form":
                switch ($_POST["cms_edit_element_action"])
                {
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
                switch ($_POST["cms_page_selected_action"])
                {
                    case "submit":
                        $data = $elementsControllerService->editElementData($_POST);
                        break;
                    default:
                        $data = $elementsControllerService->getElementData($_POST);
                        break;
                }
                break;
            default:
                $data = $elementsControllerService->getElementData($_POST);
                break;
        }
        
        Oforge()->View()->assign($data);
    }

}
