<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 07.01.2019
 * Time: 15:57
 */

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Slim\Http\Request;
use Slim\Http\Response;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;

class ElementsController extends AbstractController {
    
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        $elementsControllerService = OForge()->Services()->get("elements.controller.service");

        $data = $elementsControllerService->getElementData();
        
        switch ($_POST["cms_form"])
        {
            case "cms_element_jstree_form":
                $data = $elementsControllerService->editElementData($_POST);
                break;
            case "cms_page_builder_form":
                $data = $pagesControllerService->editContentData($_POST);
                break;
        }
        
        Oforge()->View()->assign($data);
    }
}