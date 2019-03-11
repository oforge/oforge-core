<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Page\Page;

class PagesControllerService extends AbstractDatabaseAccess {
    public function __construct() {
        parent::__construct(["default" => Page::class]);
    }
    
    public function getSelectedPageData($post)
    {
        $pageTreeService    = OForge()->Services()->get("page.tree.service");
        $pageBuilderService = OForge()->Services()->get("page.builder.service");
        $contentTypeService = OForge()->Services()->get("content.type.service");
        
        $selectedPage       = isset($post["cms_page_jstree_selected_page"]) && $post["cms_page_jstree_selected_page"] > 0 ? $post["cms_page_jstree_selected_page"] : 0;
        $selectedLanguage   = isset($post["cms_page_selected_language"])    && $post["cms_page_selected_language"] > 0    ? $post["cms_page_selected_language"]    : 0;
        $selectedElement    = isset($post["cms_page_selected_element"])     && !empty($post["cms_page_selected_element"]) ? $post["cms_page_selected_element"]     : 0;
        $selectedAction     = isset($post["cms_page_selected_action"])      && !empty($post["cms_page_selected_action"])  ? $post["cms_page_selected_action"]      : 'edit';
        
        $data = [
            "js"                => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "pages"             => $pageTreeService->getPageArray(),
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "selectedElement"   => $selectedElement,
            "post"              => $post
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
                            $data["contentElementData"] = $contentTypeService->setContentDataArray($selectedElementId, $selectedElementTypeId, $post)->getContentDataArray($selectedElementId, $selectedElementTypeId);
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
        
        return $data;
    }
}