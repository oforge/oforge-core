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
        $selectedLanguage   = isset($_POST["cms_page_selected_language"])    && $_POST["cms_page_selected_language"] > 0    ? $_POST["cms_page_selected_language"]    : 0;
        $selectedElement    = isset($_POST["cms_page_selected_element"])     && !empty($_POST["cms_page_selected_element"]) ? $_POST["cms_page_selected_element"]     : 0;
        $selectedAction     = isset($_POST["cms_page_selected_action"])      && !empty($_POST["cms_page_selected_action"])  ? $_POST["cms_page_selected_action"]      : 'edit';
        
        $data = [
            "js"                => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "pages"             => $pageTreeService->getPageArray(),
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "selectedElement"   => $selectedElement,
            "post"              => $_POST
        ];

        if ($selectedPage)
        {
            $pageArray        = $pageBuilderService->getPageArray($selectedPage);
            $pageContents     = $pageArray["paths"][$selectedLanguage]["pageContent"];
            
            if ($selectedElement)
            {
                $data["contents"] = $pageBuilderService->getContentDataArrayById($pageContents, $selectedElement);
                
                $selectedElementIdArray = explode("-", $selectedElement);
                $selectedElementId = end($selectedElementIdArray);
                
                $data["__selectedElement"] = $selectedElement; // TODO: just used as development info
                $data["__selectedElementId"] = $selectedElementId; // TODO: just used as development info
                $data["__selectedElementTypeId"] = $data["contents"]["typeId"]; // TODO: just used as development info
                $data["__selectedAction"] = $selectedAction; // TODO: just used as development info
                
                if (is_numeric($selectedElementId) && $selectedElementId > 0)
                {
                    $selectedElementTypeId = $data["contents"]["typeId"];
                    
                    if (!is_numeric($selectedElementTypeId))
                    {
                        $selectedElementTypeId = 0;
                    }
                    
                    switch ($selectedAction)
                    {
                        case "submit":
                            // persist new content element data to database and reload content data from database
                            $data["contentElementData"] = $contentTypeService->setContentDataArray($selectedElementId, $selectedElementTypeId, [])->getContentDataArray($selectedElementId, $selectedElementTypeId);
                            $data["contents"]           = $pageBuilderService->getContentDataArrayById($pageContents, $selectedElement);
                            break;
                        case "delete":
                            break;
                        default:
                            // action equals 'edit' or is unknown
                            $data["contentElementData"] = $contentTypeService->getContentDataArray($selectedElementId, $selectedElementTypeId);
                            break;
                    }
                }
            }
            else
            {
                $data["contents"] = $pageBuilderService->getContentDataArray($pageContents);
            }
            
            $data["pageBuilderData"] = $pageArray; // TODO: just used as development info
        }
        
        Oforge()->View()->assign($data);
    }
}