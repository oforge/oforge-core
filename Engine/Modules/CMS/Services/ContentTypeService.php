<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.01.2019
 * Time: 10:52
 */

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;


class ContentTypeService
{

    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(ContentTypeGroup::class);
    }
    
    /**
     * Returns all available content type group entities
     *
     * @return ContentTypeGroup[]|NULL
     */
    private function getContentTypeGroupEntities()
    {
        $contentTypeGroups = $this->repository->findAll();
        
        if (isset($contentTypeGroups))
        {
            return $contentTypeGroups;
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Returns all found content type groups as an associative array
     *
     * @return array|NULL Array filled with available content type groups
     */
    public function getContentTypeGroupArray()
    {
        $contentTypeGroupEntities = $this->getContentTypeGroupEntities();
        
        if (!$contentTypeGroupEntities)
        {
            return NULL;
        }
        
        $contentTypeGroups = [];
        foreach($contentTypeGroupEntities as $contentTypeGroupEntity)
        {
            $contentTypeGroup = [];
            $contentTypeGroup["id"] = $contentTypeGroupEntity->getId();
            $contentTypeGroup["description"] = $contentTypeGroupEntity->getDescription();
            
            $contentTypeEntities = $contentTypeGroupEntity->getContentTypes();
            
            $contentTypes = [];
            foreach($contentTypeEntities as $contentTypeEntity)
            {
                $contentType = [];
                $contentType["id"] = $contentTypeEntity->getId();
                $contentType["group"] = $contentTypeEntity->getGroup();
                $contentType["name"] = $contentTypeEntity->getName();
                $contentType["icon"] = $contentTypeEntity->getIcon();
                $contentType["description"] = $contentTypeEntity->getDescription();
                $contentType["class"] = $contentTypeEntity->getClassPath();
                
                $contentTypes[] = $contentType;
            }
            
            $contentTypeGroup["types"] = $contentTypes;
            
            $contentTypeGroups[] = $contentTypeGroup;
        }
        
        return $contentTypeGroups;
    }
}