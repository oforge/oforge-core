<?php

namespace Oforge\Engine\Modules\CMS\ContentTypes;

use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;

class RichText extends AbstractContentType
{
    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    public function isContainer(): bool
    {
        return false;
    }
    
    /**
     * Return edit data for page builder of content type
     *
     * @return string
     */
    public function getEditData()
    {
        $data = [];
        $data["id"]     = $this->getContentId();
        $data["type"]   = $this->getId();
        $data["parent"] = $this->getContentParentId();
        $data["name"]   = $this->getContentName();
        $data["css"]    = $this->getContentCssClass();
        $data["text"]   = $this->getContentData();
        
        return $data;
    }
    
    /**
     * Set edit data for page builder of content type
     * @param string $richText
     *
     * @return ContentType $this
     */
    public function setEditData($data)
    {
        $this->setContentCssClass($data['css']);
        $this->setContentData($data['text']);
        
        return $this;
    }
    
    /**
     * Return data for page rendering of content type
     *
     * @return string
     */
    public function getRenderData()
    {
        $data = [];
        $data["form"]   = "ContentTypes/" . $this->getPath() . "/PageBuilderForm.twig";
        $data["type"]   = "ContentTypes/" . $this->getPath() . "/PageBuilder.twig";
        $data["typeId"] = $this->getId();
        $data["css"]    = $this->getContentCssClass();
        $data["text"]   = $this->getContentData();
        
        return $data;
    }
    
    /**
     * Return child data of content type
     *
     * @return array|false should return false if no child content data is available
     */
    public function getChildData()
    {
        return false;
    }
}
