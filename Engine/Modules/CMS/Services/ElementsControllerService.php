<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentParent;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

class ElementsControllerService extends AbstractDatabaseAccess {
    private $entityManager = NULL;
    
    public function __construct() {
        parent::__construct(["contentTypeGroup" => ContentTypeGroup::class, "contentType" => ContentType::class, "contentParent" => ContentParent::class, "content" => Content::class]);
        
        $this->entityManager = Oforge()->DB()->getEntityManager();
    }

    private function getPageContentArrayForContentElement($contentId)
    {
        $contentTypeService = OForge()->Services()->get("content.type.service");
        $pageBuilderService = OForge()->Services()->get("page.builder.service");

        $contentEntity = $this->repository('content')->findOneBy(["id" => $contentId]);

        if ($contentEntity) {
            $pageContents = [];

            $pageContent            = [];
            $pageContent["id"]      = 0;
            $pageContent["content"] = $pageBuilderService->getContentArray($contentEntity);
            $pageContent["order"]   = 0;
            
            $pageContents[] = $pageContent;

            $contentElementId     = $contentEntity->getId();
            $contentElementTypeId = $contentEntity->getType()->getId();

            $data["contentElementData"] = $contentTypeService->getContentDataArray($contentElementId, $contentElementTypeId);
            $data["contents"]           = $pageBuilderService->getContentDataArrayById($pageContents, $contentElementId);

            return $data;
        }

        return NULL;
    }

    public function editPageBuilderData($post)
    {
        $contentTypeService     = OForge()->Services()->get("content.type.service");
        $pageBuilderService     = OForge()->Services()->get("page.builder.service");
        $pagesControllerService = OForge()->Services()->get("pages.controller.service");

        $selectedElementId         = isset($post["cms_page_selected_element"])              && !empty($post["cms_page_selected_element"])               ? $post["cms_page_selected_element"] : false;
        $createContentWithTypeId   = isset($post["cms_page_create_content_with_type_id"])   && $post["cms_page_create_content_with_type_id"] > 0        ? $post["cms_page_create_content_with_type_id"]   : 0;
        $createContentAtOrderIndex = isset($post["cms_page_create_content_at_order_index"]) && $post["cms_page_create_content_at_order_index"] > 0      ? $post["cms_page_create_content_at_order_index"] : 0;
        $deleteContentWithId       = isset($post["cms_page_delete_content_with_id"])        && !empty($post["cms_page_delete_content_with_id"])         ? $post["cms_page_delete_content_with_id"]        : 0;
        $deleteContentAtOrderIndex = isset($post["cms_page_delete_content_at_order_index"]) && !empty($post["cms_page_delete_content_at_order_index"])  ? $post["cms_page_delete_content_at_order_index"] : 0;
        $selectedAction            = isset($post["cms_page_selected_action"])               && !empty($post["cms_page_selected_action"])                ? $post["cms_page_selected_action"]  : false;

        $data = [];
        $data["__selectedElementId"] = $selectedElementId; // TODO: just used as development info
        $data["__createContentWithTypeId"] = $createContentWithTypeId; // TODO: just used as development info
        $data["__createContentAtOrderIndex"] = $createContentAtOrderIndex; // TODO: just used as development info
        $data["__deleteContentWithId"] = $deleteContentWithId; // TODO: just used as development info
        $data["__deleteContentAtOrderIndex"] = $deleteContentAtOrderIndex; // TODO: just used as development info
        $data["__selectedAction"] = $selectedAction; // TODO: just used as development info

        switch ($selectedAction) {
            case "create":
                // create new content type in selected container
                $newContentId = $pagesControllerService->createContentElement(false, $selectedElementId, $createContentWithTypeId, $createContentAtOrderIndex);

                // reload content data from database
                $post = [];
                $post["cms_edit_element_action"] = "edit";
                $post["cms_edit_element_id"] = $newContentId;
                $post["cms_form"] = "cms_element_jstree_form";

                return $this->editElementData($post);
            case "delete":
                // delete content in selected container
                $pagesControllerService->deleteContentElement(false, $selectedElementId, $deleteContentWithId, $deleteContentAtOrderIndex);

                // reload content data from database
                $post = [];
                $post["cms_edit_element_action"] = "edit";
                $post["cms_edit_element_id"] = $selectedElementId;
                $post["cms_form"] = "cms_element_jstree_form";

                return $this->editElementData($post);
            case "submit":
                $selectedElementId = isset($post["cms_page_selected_element"]) && !empty($post["cms_page_selected_element"]) ? $post["cms_page_selected_element"] : false;

                if ($selectedElementId > 0) {
                    $contentEntity = $this->repository('content')->findOneBy(["id" => $selectedElementId]);

                    $selectedElementTypeId = false;
    
                    if ($contentEntity) {
                        $selectedElementTypeId = $contentEntity->getType()->getId();
                    }
    
                    if ($selectedElementTypeId) {
                        // persist new content element data to database
                        $contentTypeService->setContentDataArray($selectedElementId, $selectedElementTypeId, $post);

                        // reload content data from database
                        $post = [];
                        $post["cms_edit_element_action"] = "edit";
                        $post["cms_edit_element_id"] = $selectedElementId;
                        $post["cms_form"] = "cms_element_jstree_form";
        
                        return $this->editElementData($post);
                    }
                }
                break;
        }
    }

    private function resetContentElementContentParent($contentParentEntity)
    {
        $contentEntities = $this->repository('content')->findBy(["parent" => $contentParentEntity]);

        foreach ($contentEntities as $contentEntity)
        {
            $contentEntity->setParent(NULL);
            
            $this->entityManager->persist($contentEntity);
            $this->entityManager->flush();
        }
    }

    /**
     * @param $parentId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function findAndRemoveChildContentParents($parentId)
    {
        $contentParentEntities = $this->repository('contentParent')->findBy(["parent" => $parentId]);
        
        foreach ($contentParentEntities as $contentParentEntity)
        {
            $this->findAndRemoveChildContentParents($contentParentEntity->getId());

            $this->resetContentElementContentParent($contentParentEntity);
            
            $this->entityManager->remove($contentParentEntity);
            $this->entityManager->flush();
        }
    }

    private function moveContentParent($selectedElementId, $selectedElementParentId) {
        $moveContentParentId = intval(str_replace("_parent#", "", $selectedElementId));
        $toContentParentId   = intval(str_replace("_parent#", "", $selectedElementParentId));

        if ($moveContentParentId > 0 && $toContentParentId > 0)
        {
            $moveContentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $moveContentParentId]);
            $toContentParentEntity   = $this->repository('contentParent')->findOneBy(["id" => $toContentParentId]);

            if ($moveContentParentEntity && $toContentParentEntity)
            {
                $moveContentParentEntity->setParent($toContentParentEntity);
                
                $this->entityManager->persist($moveContentParentEntity);
                $this->entityManager->flush();
            }
        }
        elseif ($moveContentParentId > 0 && $selectedElementParentId === "#")
        {
            $moveContentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $moveContentParentId]);

            if ($moveContentParentEntity)
            {
                $moveContentParentEntity->setParent(NULL);
                
                $this->entityManager->persist($moveContentParentEntity);
                $this->entityManager->flush();
            }
        }
    }

    private function moveContentElement($selectedElementId, $selectedElementParentId) {
        $moveContentElementId = intval(str_replace("_element#", "", $selectedElementId));
        $toContentParentId    = intval(str_replace("_parent#", "", $selectedElementParentId));

        if ($moveContentElementId > 0 && $toContentParentId > 0)
        {
            $moveContentElementEntity = $this->repository('content')->findOneBy(["id" => $moveContentElementId]);
            $toContentParentEntity    = $this->repository('contentParent')->findOneBy(["id" => $toContentParentId]);

            if ($moveContentElementEntity && $toContentParentEntity)
            {
                $moveContentElementEntity->setParent($toContentParentEntity);
                
                $this->entityManager->persist($moveContentElementEntity);
                $this->entityManager->flush();
            }
        }
        elseif ($moveContentElementId > 0 && strpos($selectedElementParentId, "_parent#") !== 0)
        {
            $moveContentElementEntity = $this->repository('content')->findOneBy(["id" => $moveContentElementId]);

            if ($moveContentElementEntity)
            {
                $moveContentElementEntity->setParent(NULL);
                
                $this->entityManager->persist($moveContentElementEntity);
                $this->entityManager->flush();
            }
        }
    }

    public function createContentElement($post) {
        $selectedElementId          = isset($post["cms_edit_element_id"])          && !empty($post["cms_edit_element_id"])          ? $post["cms_edit_element_id"]          : false;
        $selectedElementParentId    = isset($post["cms_edit_element_parent_id"])   && !empty($post["cms_edit_element_parent_id"])   ? $post["cms_edit_element_parent_id"]   : false;

        $selectedElementId   = intval($selectedElementId);
        $contentParentEntity = NULL;

        if (strpos($selectedElementParentId, "_parent#") === 0)
        {
            $selectedElementParentId = intval(str_replace("_parent#", "", $selectedElementParentId));
        
            if ($selectedElementParentId > 0)
            {
                $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementParentId]);
            }
        }

        $contentTypeEntity = $this->repository('contentType')->findOneBy(["id" => $selectedElementId]);
        
        $contentId = false;
        
        if ($contentTypeEntity) {
            $contentEntity =  new Content;
            $contentEntity->setType($contentTypeEntity);
            $contentEntity->setParent($contentParentEntity);
            $contentEntity->setName(uniqid());
            $contentEntity->setCssClass('');
            
            $this->entityManager->persist($contentEntity);
            $this->entityManager->flush();
            
            $contentId = $contentEntity->getId();
        }

        unset($post);

        return $this->editElementData($post);
    }

    public function moveElementData($post)
    {
        $selectedElementId          = isset($post["cms_edit_element_id"])          && !empty($post["cms_edit_element_id"])          ? $post["cms_edit_element_id"]          : false;
        $selectedElementParentId    = isset($post["cms_edit_element_parent_id"])   && !empty($post["cms_edit_element_parent_id"])   ? $post["cms_edit_element_parent_id"]   : false;

        if (strpos($selectedElementId, "_parent#") === 0)
        {
            $this->moveContentParent($selectedElementId, $selectedElementParentId);
        } elseif (strpos($selectedElementId, "_element#") === 0) {
            $this->moveContentElement($selectedElementId, $selectedElementParentId);
        }

        unset($post);

        return $this->editElementData($post);
    }

    public function editElementData($post)
    {
        $elementTreeService = OForge()->Services()->get("element.tree.service");
        $contentTypeService = OForge()->Services()->get("content.type.service");

        $selectedElementId          = isset($post["cms_edit_element_id"])          && !empty($post["cms_edit_element_id"])          ? $post["cms_edit_element_id"]          : false;
        $selectedElementParentId    = isset($post["cms_edit_element_parent_id"])   && !empty($post["cms_edit_element_parent_id"])   ? $post["cms_edit_element_parent_id"]   : false;
        $selectedElementDescription = isset($post["cms_edit_element_description"]) && !empty($post["cms_edit_element_description"]) ? $post["cms_edit_element_description"] : false;
        $selectedAction             = isset($post["cms_edit_element_action"])      && !empty($post["cms_edit_element_action"])      ? $post["cms_edit_element_action"]      : false;

        if ($selectedAction === "edit" && $selectedElementId > 0)
        {
            $post["cms_page_selected_element"] = $selectedElementId;
        }

        $data = [];

        switch($selectedAction)
        {
            case 'create':
                $selectedElementParentId = intval(str_replace("_parent#", "", $selectedElementParentId));

                if (is_numeric($selectedElementParentId) && is_int($selectedElementParentId) && $selectedElementParentId > 0)
                {
                    $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementParentId]);
                }
                else
                {
                    $contentParentEntity = NULL;
                }

                $newContentParentEntity = new ContentParent;
                $newContentParentEntity->setParent($contentParentEntity);
                $newContentParentEntity->setDescription($selectedElementDescription);
                
                $this->entityManager->persist($newContentParentEntity);
                $this->entityManager->flush();
                
                $contentParentId = $newContentParentEntity->getId();
                break;
            case 'rename':
                $selectedElementId = intval(str_replace("_parent#", "", $selectedElementId));

                if (is_numeric($selectedElementId) && is_int($selectedElementId) && $selectedElementId > 0)
                {
                    $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementId]);

                    if ($contentParentEntity)
                    {
                        $contentParentEntity->setDescription($selectedElementDescription);
                
                        $this->entityManager->persist($contentParentEntity);
                        $this->entityManager->flush();
                    }
                }
                break;
            case 'delete':
                $selectedElementId = intval(str_replace("_parent#", "", $selectedElementId));

                if (is_numeric($selectedElementId) && is_int($selectedElementId) && $selectedElementId > 0)
                {
                    $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementId]);

                    if ($contentParentEntity)
                    {
                        $this->findAndRemoveChildContentParents($contentParentEntity->getId());
    
                        $this->resetContentElementContentParent($contentParentEntity);
                        
                        $this->entityManager->remove($contentParentEntity);
                        $this->entityManager->flush();
                    }
                }
                break;
            case 'edit':
                $data = $this->getPageContentArrayForContentElement($selectedElementId);
                break;
        }

        $data["post"] = $post; // TODO: just used as development info
        $data["__selectedElementId"] = $selectedElementId; // TODO: just used as development info
        $data["__selectedElementParentId"] = $selectedElementParentId; // TODO: just used as development info
        $data["__selectedElementDescription"] = $selectedElementDescription; // TODO: just used as development info
        $data["__selectedAction"] = $selectedAction; // TODO: just used as development info

        $data["js"]                = ["cms_elements_controller_jstree_config" => $elementTreeService->generateJsTreeConfigJSON()];
        $data["contentTypeGroups"] = $contentTypeService->getContentTypeGroupArray();

        return $data;
    }
}
