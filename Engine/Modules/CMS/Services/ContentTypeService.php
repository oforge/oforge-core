<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\ContentTypes\Row;

class ContentTypeService extends AbstractDatabaseAccess
{
    private $entityManager;
    private $repository;

    public function __construct()
    {
        parent::__construct(['contentTypeGroup' => ContentTypeGroup::class, 'contentType' => ContentType::class, 'content' => Content::class, 'row' => Row::class]);
    }

    /**
     * Returns all available content type group entities
     *
     * @return ContentTypeGroup[]|NULL
     */
    private function getContentTypeGroupEntities()
    {
        $contentTypeGroups = $this->repository('contentTypeGroup')->findAll();
        
        if (isset($contentTypeGroups))
        {
            return $contentTypeGroups;
        }
        else
        {
            return null;
        }
    }

    /**
     * Returns all found content type groups as an associative array
     *use phpDocumentor\Reflection\Types\Null_;

     * @return array|NULL Array filled with available content type groups
     */
    public function getContentTypeGroupArray()
    {
        $contentTypeGroupEntities = $this->getContentTypeGroupEntities();

        if (!$contentTypeGroupEntities)
        {
            return null;
        }

        $contentTypeGroups = [];
        foreach ($contentTypeGroupEntities as $contentTypeGroupEntity) {
            $contentTypeGroup                = [];
            $contentTypeGroup['id']          = $contentTypeGroupEntity->getId();
            $contentTypeGroup['description'] = $contentTypeGroupEntity->getDescription();

            $contentTypeEntities = $contentTypeGroupEntity->getContentTypes();
          
            $contentTypes = [];
            foreach($contentTypeEntities as $contentTypeEntity)
            {
                $contentType                = [];
                $contentType['id']          = $contentTypeEntity->getId();
                $contentType['group']       = $contentTypeEntity->getGroup();
                $contentType['name']        = $contentTypeEntity->getName();
                $contentType['icon']        = $contentTypeEntity->getIcon();
                $contentType['description'] = $contentTypeEntity->getDescription();
                $contentType['class']       = $contentTypeEntity->getClassPath();
                
                $contentTypes[] = $contentType;
            }
            
            $contentTypeGroup['types'] = $contentTypes;
          
            $contentTypeGroups[] = $contentTypeGroup;
        }

        return $contentTypeGroups;
    }
    
    /**
     * Returns an array with all data for the selected content element
     * @param int $id of the selected content element
     *
     * @return array|NULL Array filled with all data for the selected content element
     */
    public function getContentDataArray(int $id, int $typeId)
    {
        $contentTypeEntity = $this->repository('contentType')->findOneBy(["id" => $typeId]);
        $contentEntity     = $this->repository('content')->findOneBy(["id" => $id]);
        
        if ($contentTypeEntity && $contentEntity && $contentTypeEntity == $contentEntity->getType())
        {
            $data = [];
            $data["id"]     = $contentEntity->getId();
            $data["type"]   = $contentEntity->getType()->getId();
            $data["parent"] = $contentEntity->getParent();
            $data["name"]   = $contentEntity->getName();
            $data["css"]    = $contentEntity->getCssClass();
            
            switch ($contentTypeEntity->getName())
            {
                case "Row":
                    $rowEntities       = $this->repository('rowContent')->findBy(["row" => $data["id"]], ["order" => "ASC"]);
                    
                    $rowColumns = [];
                    foreach ($rowEntities as $rowEntity)
                    {
                        $rowContent = [];
                        $rowContent["id"] = $rowEntity->getContent()->getId();
                        $rowContent["typeId"] = $rowEntity->getContent()->getType()->getId();
                        
                        $rowColumns[] = $rowContent;
                    }
                    $data["columns"] = $rowColumns;
                    break;
                case "RichText":
                    $data["text"]  = $contentEntity->getData();
                    break;
                case "Image":
                    $data["image"] = $contentEntity->getData();
                    break;
                default:
                    return false;
            }
        }
        
        return $data;
    }
    
    /**
     * Sets the data for the selected content element
     * @param int $id id of the selected content element
     * @param int $typeId id of the selected content element type
     * @param array $data array filled with all data for the selected content element
     *
     * @return ContentTypeService $this instance of the content type service
     */
    public function setContentDataArray(int $id, int $typeId, array $data)
    {
        // get content type of selected content element
        
        // switch depending on selected content element
        // create new instance of selected content element type
        // get content element from db if available
        // set new data in content element instance
        // store data to db
        
        return $this;
    }
}
