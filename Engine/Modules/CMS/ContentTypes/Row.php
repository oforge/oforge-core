<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;

class Row extends AbstractContentType
{
    /**
     * Return row entities for given row id
     * @param int $rowId
     *
     * @return Row[]|NULL
     */
    private function getRowEntities(int $rowId)
    {
        /** @var Row[] $rowEntities */
        $rowEntities = $this->entityManager->getRepository('Oforge\Engine\Modules\CMS\Models\ContentTypes\Row')->findBy(["row" => $rowId], ["order" => "ASC"]);
        
        if (isset($rowEntities)) {
            return $rowEntities;
        }
        return null;
    }
    
    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    public function isContainer(): bool
    {
        return true;
    }
    
    /**
     * Return edit data for page builder of content type
     *
     * @return mixed
     */
    public function getEditData()
    {
        return $data;
    }
    
    /**
     * Set edit data for page builder of content type
     * @param mixed $data
     *
     * @return ContentType $this
     */
    public function setEditData($data)
    {
        $this->setContentData($data);
        return $this;
    }
    
    /**
     * Return data for page rendering of content type
     *
     * @return mixed
     */
    public function getRenderData()
    {
        $data = [];
        $data["type"]   = "ContentTypes/" . $this->getPath() . "/PageBuilder.twig";
        $data["typeId"] = $this->getId();
        $data["css"]    = $this->getContentCssClass();
        
        return $data;
    }
    
    /**
     * Return child data of content type
     *
     * @return array|false should return false if no child content data is available
     */
    public function getChildData()
    {
        $rowEntities = $this->getRowEntities($this->getContentId());
        
        if (!$rowEntities)
        {
            return NULL;
        }
        
        $rowColumnContents = [];
        foreach($rowEntities as $rowEntity)
        {
            $rowColumnContent           = [];
            $rowColumnContent["id"]     = $rowEntity->getId();
            $rowColumnContent["content"] = $rowEntity->getContent();
            $rowColumnContent["order"]  = $rowEntity->getOrder();
            
            $rowColumnContents[] = $rowColumnContent;
        }
        
        return $rowColumnContents;
    }
}
