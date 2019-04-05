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
        
        $this->entityManager = Oforge()->DB()->getManager();
    }

    private function resetContentElementContentParent($contentParentEntity)
    {
        $contentEntities = $this->repository('content')->findBy(["parent" => $contentParentEntity]);

        foreach ($contentEntities as $contentEntity)
        {
            $contentEntity->setParent(NULL);
            
            $this->contentEntity->persist($contentParentEntity);
            $this->contentEntity->flush();
        }
    }

    /**
     * @param $parentId
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function findAndRemoveChildContentParents($parentId)
    {
        $contentParentEntities = $this->repository('contentParent')->findBy(["id" => $selectedElementParentId]);
        
        foreach ($contentParentEntities as $contentParentEntity)
        {
            $this->findAndRemoveChildContentParents($contentParentEntity->getId());

            $this->resetContentElementContentParent($contentParentEntity);
            
            $this->entityManager->remove($contentParentEntity);
            $this->entityManager->flush();
        }
    }

    public function editElementData($post)
    {
        $selectedElementId          = isset($post["cms_edit_element_id"])          && !empty($post["cms_edit_element_id"])          ? $post["cms_edit_element_id"]          : false;
        $selectedElementParentId    = isset($post["cms_edit_element_parent_id"])   && !empty($post["cms_edit_element_parent_id"])   ? $post["cms_edit_element_parent_id"]   : false;
        $selectedElementDescription = isset($post["cms_edit_element_description"]) && !empty($post["cms_edit_element_description"]) ? $post["cms_edit_element_description"] : false;
        $selectedAction             = isset($post["cms_edit_element_action"])      && !empty($post["cms_edit_element_action"])      ? $post["cms_edit_element_action"]      : false;

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
        }

        $data = [];
        $data["__selectedElementId"] = $selectedElementId; // TODO: just used as development info
        $data["__selectedElementParentId"] = $selectedElementParentId; // TODO: just used as development info
        $data["__selectedElementDescription"] = $selectedElementDescription; // TODO: just used as development info
        $data["__selectedAction"] = $selectedAction; // TODO: just used as development info

        $data = array_merge($data, $this->getElementData($post));

        //return $this->getElementData();
        return $data;
    }

    public function getElementData($post)
    {
        $elementTreeService = OForge()->Services()->get("element.tree.service");
        $contentTypeService = OForge()->Services()->get("content.type.service");
        
        $data = [
            "js"                => ["cms_elements_controller_jstree_config" => $elementTreeService->generateJsTreeConfigJSON()],
            "contentTypeGroups" => $contentTypeService->getContentTypeGroupArray(),
            "post"              => $post
        ];
        
        return $data;
    }
}