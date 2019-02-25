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
use Oforge\Engine\Modules\CMS\Services\PageTreeService;
use Oforge\Engine\Modules\CMS\Services\PageBuilderService;

class PagesController extends AbstractController {
    /**
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     * @throws \Oforge\Engine\Modules\Core\Exceptions\ServiceNotFoundException
     */
    public function indexAction(Request $request, Response $response) {
        $contentTypeService = OForge()->Services()->get("content.type.service");
        $pageTreeService    = OForge()->Services()->get("page.tree.service");
        $pageBuilderService = OForge()->Services()->get("page.builder.service");
        
        $selectedPage       = isset($_POST["cms_page_jstree_selected_page"]) && $_POST["cms_page_jstree_selected_page"] > 0 ? $_POST["cms_page_jstree_selected_page"] : 0;
        $selectedLanguage   = isset($_POST["cms_page_selected_language"]) && $_POST["cms_page_selected_language"] > 0 ? $_POST["cms_page_selected_language"] : 0;
        
        $data = [
            "js"                => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "pages"             => $pageTreeService->getPageArray(),
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "post"              => $_POST
        ];

        if ($selectedPage)
        {
            $pageArray      = $pageBuilderService->getPageArray($selectedPage);
            $pageContents   = $pageArray["paths"][$selectedLanguage]["pageContent"];
            
            $data["contents"]        = $pageBuilderService->getContentDataArray($pageContents);
            $data["pageBuilderData"] = $pageArray; // TODO: just used as development info
            
            // TODO: remove $contentFinder debug code
            $data["contentFinder"] = $pageBuilderService->getContentDataArrayById($pageContents, "17-19-21"); // 17-19-21-15
        }
        
        Oforge()->View()->assign($data);
    }
}