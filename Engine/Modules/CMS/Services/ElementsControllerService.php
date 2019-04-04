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
        $selectedElementId          = isset($post["cms_edit_element_id"])          && $post["cms_edit_element_id"] > 0              ? $post["cms_edit_element_id"]          : 0;
        $selectedElementParentId    = isset($post["cms_edit_element_parent_id"])   && $post["cms_edit_element_parent_id"] > 0       ? $post["cms_edit_element_parent_id"]   : 0;
        $selectedElementDescription = isset($post["cms_edit_element_description"]) && !empty($post["cms_edit_element_description"]) ? $post["cms_edit_element_description"] : false;
        $selectedAction             = isset($post["cms_edit_element_action"])      && !empty($post["cms_edit_element_action"])      ? $post["cms_edit_element_action"]      : false;
        
        switch($selectedAction)
        {
            case 'create':
                if (is_numeric($selectedElementParentId) && is_int($selectedElementParentId) && $selectedElementParentId > 0)
                {
                    $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementParentId]);
                }
                else
                {
                    $contentParentEntity = NULL;
                }

                $contentParentEntity = new ContentParent;
                $contentParentEntity->setParent($contentParentEntity);
                $contentParentEntity->setDescription($selectedElementDescription);
                
                $this->entityManager->persist($contentParentEntity);
                $this->entityManager->flush();
                
                $contentParentId = $contentParentEntity->getId();
                break;
            case 'rename':
                $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementParentId]);

                if ($contentParentEntity)
                {
                    $contentParentEntity->setDescription($selectedElementDescription);
            
                    $this->entityManager->persist($contentParentEntity);
                    $this->entityManager->flush();
                }
                break;
            case 'delete':
                $contentParentEntity = $this->repository('contentParent')->findOneBy(["id" => $selectedElementParentId]);

                if ($contentParentEntity)
                {
                    $this->findAndRemoveChildContentParents($contentParentEntity->getId());

                    $this->resetContentElementContentParent($contentParentEntity);
                    
                    $this->entityManager->remove($contentParentEntity);
                    $this->entityManager->flush();
                }
                break;
        }

        return $this->getElementData();
    }

    public function getElementData()
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