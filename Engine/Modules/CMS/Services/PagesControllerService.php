<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Site\Site;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;

class PagesControllerService extends AbstractDatabaseAccess {
    private $entityManager = NULL;
    
    public function __construct() {
        parent::__construct(["language" => Language::class, "layout" => Layout::class, "site" => Site::class, "page" => Page::class, "pagePath" => PagePath::class, "pageContent" => PageContent::class, "contentType" => ContentType::class, "content" => Content::class]);
        
        $this->entityManager = Oforge()->DB()->getManager();
    }
    
    public function getAvailableLanuages()
    {
        $languageEntities = $this->repository('language')->findAll();
        
        $languages = [];
        foreach ($languageEntities as $languageEntity)
        {
            $language = [];
            $language["id"] = $languageEntity->getId();
            $language["iso"] = $languageEntity->getIso();
            $language["name"] = $languageEntity->getName();
            $language["active"] = $languageEntity->isActive();
            
            $languages[] = $language;
        }
        
        return $languages;
    }
    
    public function getDefaultLanguageForPage($id)
    {
        $pageEntity = $this->repository('page')->findOneBy(["id" => $id]);
        
        if ($pageEntity)
        {
            $siteEntity = $this->repository('site')->findOneBy(["id" => $pageEntity->getSite()]);
            
            if ($siteEntity)
            {
                return $siteEntity->getId();
            }
        }
        
        return 0;
    }
    
    private function findAndRemoveChildPages($parentId)
    {
        $pageEntities = $this->repository('page')->findBy(["parent" => $parentId]);
        
        foreach ($pageEntities as $pageEntity)
        {
            $this->findAndRemoveChildPages($pageEntity->getId());
            
            $this->entityManager->remove($pageEntity);
            $this->entityManager->flush();
        }
    }
    
    public function editPageData($post)
    {
        $pageTreeService = OForge()->Services()->get("page.tree.service");
        
        $selectedPageId       = isset($post["cms_edit_page_id"])        && $post["cms_edit_page_id"] > 0         ? $post["cms_edit_page_id"]        : 0;
        $selectedPageParentId = isset($post["cms_edit_page_parent_id"]) && $post["cms_edit_page_parent_id"] > 0  ? $post["cms_edit_page_parent_id"] : 0;
        $selectedPageName     = isset($post["cms_edit_page_name"])      && !empty($post["cms_edit_page_name"])   ? $post["cms_edit_page_name"]      : false;
        $selectedAction       = isset($post["cms_edit_page_action"])    && !empty($post["cms_edit_page_action"]) ? $post["cms_edit_page_action"]    : false;
        
        switch($selectedAction)
        {
            case 'create':
                // TODO: get selected layout and site instead of using default ones 
                $layoutEntity = $this->repository('layout')->findOneBy(["id" => 1]);
                $siteEntity   = $this->repository('site')->findOneBy(["id" => 1]);
                
                if ($layoutEntity && $siteEntity)
                {
                    $pageEntity = new Page;
                    $pageEntity->setLayout($layoutEntity->getId());
                    $pageEntity->setSite($siteEntity->getId());
                    $pageEntity->setParent($selectedPageParentId);
                    $pageEntity->setName($selectedPageName);
                    
                    $this->entityManager->persist($pageEntity);
                    $this->entityManager->flush();
                    
                    $pageId = $pageEntity->getId();
                }
                break;
            case 'rename':
                $pageEntity = $this->repository('page')->findOneBy(["id" => $selectedPageId]);
                
                if ($pageEntity)
                {
                    $pageEntity->setName($selectedPageName);
                    
                    $this->entityManager->persist($pageEntity);
                    $this->entityManager->flush();
                }
                break;
            case 'delete':
                $pageEntity = $this->repository('page')->findOneBy(["id" => $selectedPageId]);

                if ($pageEntity)
                {
                    $this->findAndRemoveChildPages($pageEntity->getId());
                    
                    $this->entityManager->remove($pageEntity);
                    $this->entityManager->flush();
                }
                break;
        }
        
        $data = [
            "js"   => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "post" => $post
        ];
        
        $data["__newlyCreatedPageId"] = $pageId; // TODO: just used as development info
        $data["__selectedPageId"] = $selectedPageId; // TODO: just used as development info
        $data["__selectedPageParentId"] = $selectedPageParentId; // TODO: just used as development info
        $data["__selectedPageName"] = $selectedPageName; // TODO: just used as development info
        $data["__selectedAction"] = $selectedAction; // TODO: just used as development info
        
        return $data;
    }
    
    public function checkForValidPagePath($post)
    {
        $selectedPage       = isset($post["cms_page_jstree_selected_page"]) && $post["cms_page_jstree_selected_page"] > 0 ? $post["cms_page_jstree_selected_page"] : 0;
        $selectedLanguage   = isset($post["cms_page_selected_language"])    && $post["cms_page_selected_language"] > 0    ? $post["cms_page_selected_language"]    : $post["cms_page_selected_language"] = $this->getDefaultLanguageForPage($selectedPage);
        
        $pagePathEntity = $this->repository('pagePath')->findOneBy(["page" => $selectedPage, "language" => $selectedLanguage]);
        
        if ($pagePathEntity)
        {
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function editPagePathData($post)
    {
        $pageTreeService = OForge()->Services()->get("page.tree.service");
        
        $selectedPage = isset($post["cms_page_jstree_selected_page"]) && $post["cms_page_jstree_selected_page"] > 0 ? $post["cms_page_jstree_selected_page"] : 0;
        
        if (!isset($post["cms_page_selected_language"]) || $post["cms_page_selected_language"] < 1)
        {
            $post["cms_page_selected_language"] = $this->getDefaultLanguageForPage($selectedPage);
        }
        
        $data = [
            "js"                      => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "languages"               => $this->getAvailableLanuages(),
            "cms_page_builder_action" => "edit_page_path",
            "post"                    => $post
        ];
        
        return $data;
    }
    
    public function updatePagePathData($post)
    {
        $selectedPage       = isset($post["cms_page_jstree_selected_page"]) && $post["cms_page_jstree_selected_page"] > 0 ? $post["cms_page_jstree_selected_page"] : 0;
        $selectedLanguage   = isset($post["cms_page_selected_language"])    && $post["cms_page_selected_language"] > 0    ? $post["cms_page_selected_language"]    : $post["cms_page_selected_language"] = $this->getDefaultLanguageForPage($selectedPage);
        $pagePath           = isset($post["cms_page_data_page_path"])       && !empty($post["cms_page_data_page_path"])   ? $post["cms_page_data_page_path"]       : false;

        if ($pagePath)
        {
            $pageEntity     = $this->repository('page')->findOneBy(["id" => $selectedPage]);
            $languageEntity = $this->repository('language')->findOneBy(["id" => $selectedLanguage]);
            
            if ($pageEntity && $languageEntity)
            {
                $pagePathEntity = $this->repository('pagePath')->findOneBy(["page" => $selectedPage, "language" => $selectedLanguage]);
                
                if (!$pagePathEntity)
                {
                    $pagePathEntity = new PagePath;
                    $pagePathEntity->setPage($pageEntity);
                    $pagePathEntity->setLanguage($languageEntity);
                }
                
                $pagePathEntity->setPath($pagePath);
                
                $this->entityManager->persist($pagePathEntity);
                $this->entityManager->flush();
            }
        }
    }
    
    private function createContentElement($pagePathId, $selectedElementId, $createContentWithTypeId, $createContentAtOrderIndex)
    {
        $contentTypeEntity = $this->repository('contentType')->findOneBy(["id" => $createContentWithTypeId]);
        
        if ($contentTypeEntity)
        {
            $contentEntity =  new Content;
            $contentEntity->setType($contentTypeEntity);
            $contentEntity->setParent(0);
            $contentEntity->setName(uniqid());
            $contentEntity->setCssClass('');
            
            $this->entityManager->persist($contentEntity);
            $this->entityManager->flush();
            
            $contentId = $contentEntity->getId();
            
            if ($selectedElementId < 1)
            {
                $pageContentEntities = $this->repository('pageContent')->findBy(["pagePath" => $pagePathId]);
                
                if ($pageContentEntities)
                {
                    foreach ($pageContentEntities as $pageContentEntity)
                    {
                        $currentOrder = $pageContentEntity->getOrder();
                        if ($currentOrder >= $createContentAtOrderIndex)
                        {
                            $pageContentEntity->setOrder($currentOrder + 1);
                            
                            $this->entityManager->persist($pageContentEntity);
                            $this->entityManager->flush();
                        }
                    }
                }
                
                $pagePathEntity = $this->repository('pagePath')->findOneBy(["id" => $pagePathId]);
                
                if ($pagePathEntity)
                {
                    $pageContentEntity =  new PageContent;
                    $pageContentEntity->setPagePath($pagePathEntity);
                    $pageContentEntity->setContent($contentEntity);
                    $pageContentEntity->setOrder($createContentAtOrderIndex);
                    
                    $this->entityManager->persist($pageContentEntity);
                    $this->entityManager->flush();
                }
            }
            else
            {
                $containerContentEntity = $this->repository('content')->findOneBy(["id" => $selectedElementId]);
                
                if ($containerContentEntity)
                {
                    $contentTypeClassPath = $containerContentEntity->getType()->getClassPath();
                    
                    $containerContentTypeEntity = new $contentTypeClassPath;
                    
                    if ($containerContentTypeEntity)
                    {
                        $containerContentTypeEntity->load($containerContentEntity->getId());
                        $containerContentTypeEntity->createChild($contentEntity, $createContentAtOrderIndex);
                    }
                }
            }
        }
    }
    
    public function editContentData($post)
    {
        $pageTreeService    = OForge()->Services()->get("page.tree.service");
        $pageBuilderService = OForge()->Services()->get("page.builder.service");
        $contentTypeService = OForge()->Services()->get("content.type.service");
        
        $selectedPage              = isset($post["cms_page_jstree_selected_page"])          && $post["cms_page_jstree_selected_page"] > 0           ? $post["cms_page_jstree_selected_page"]          : 0;
        $selectedLanguage          = isset($post["cms_page_selected_language"])             && $post["cms_page_selected_language"] > 0              ? $post["cms_page_selected_language"]             : $post["cms_page_selected_language"] = $this->getDefaultLanguageForPage($selectedPage);
        $selectedElement           = isset($post["cms_page_selected_element"])              && !empty($post["cms_page_selected_element"])           ? $post["cms_page_selected_element"]              : 0;
        $createContentWithTypeId   = isset($post["cms_page_create_content_with_type_id"])   && $post["cms_page_create_content_with_type_id"] > 0    ? $post["cms_page_create_content_with_type_id"]   : 0;
        $createContentAtOrderIndex = isset($post["cms_page_create_content_at_order_index"]) && $post["cms_page_create_content_at_order_index"] > 0  ? $post["cms_page_create_content_at_order_index"] : 0;
        $selectedAction            = isset($post["cms_page_selected_action"])               && !empty($post["cms_page_selected_action"])            ? $post["cms_page_selected_action"]               : 'edit';
        
        $data = [
            "js"                => ["cms_page_controller_jstree_config" => $pageTreeService->generateJsTreeConfigJSON()],
            "languages"         => $this->getAvailableLanuages(),
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
                        case "create":
                            $this->createContentElement($pageArray["paths"][$selectedLanguage]["id"], $selectedElementId, $createContentWithTypeId, $createContentAtOrderIndex);
                            $data["contentElementData"] = $contentTypeService->getContentDataArray($selectedElementId, $selectedElementTypeId);
                            break;
                        case "submit":
                            // persist new content element data to database and reload content data from database
                            $data["contentElementData"] = $contentTypeService->setContentDataArray($selectedElementId, $selectedElementTypeId, $post)->getContentDataArray($selectedElementId, $selectedElementTypeId);
                            $data["contents"]           = $pageBuilderService->getContentDataArrayById($pageContents, $selectedElement);
                            break;
                        case "delete":
                            $data["contentElementData"] = $contentTypeService->getContentDataArray($selectedElementId, $selectedElementTypeId);
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
                switch ($selectedAction)
                {
                    case "create":
                        $this->createContentElement($pageArray["paths"][$selectedLanguage]["id"], $selectedElementId, $createContentWithTypeId, $createContentAtOrderIndex);
                        break;
                }
                
                $data["contents"] = $pageBuilderService->getContentDataArray($pageContents);
            }
            
            $data["pageBuilderData"] = $pageArray; // TODO: just used as development info
        }
        
        return $data;
    }
}