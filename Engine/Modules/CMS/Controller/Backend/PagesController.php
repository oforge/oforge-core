<?php

namespace Oforge\Engine\Modules\CMS\Controller\Backend;

use Oforge\Engine\Modules\CMS\Services\PagesControllerService;
use Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;
use Oforge\Engine\Modules\Core\Abstracts\AbstractController;

class PagesController extends AbstractController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        /** @var PagesControllerService $pagesControllerService */
        $pagesControllerService = OForge()->Services()->get("pages.controller.service");
        
        switch ($_POST["cms_form"])
        {
            case "cms_page_jstree_form":
                $data = $pagesControllerService->editPageData($_POST);
                break;
            case "cms_page_data_form":
                $data = $pagesControllerService->updatePagePathData($_POST);
                // DO NOT INSERT A BREAK HERE SO THAT EDIT MODE IS ACTIVATED
                // AFTER UPDATING PAGE DATA
            case "cms_page_builder_form":
            default:
                if ($pagesControllerService->checkForValidPagePath($_POST))
                {
                    $data = $pagesControllerService->editContentData($_POST);
                }
                else
                {
                    $data = $pagesControllerService->editPagePathData($_POST);
                }
                break;
        }
        
        Oforge()->View()->assign($data);
    }
}