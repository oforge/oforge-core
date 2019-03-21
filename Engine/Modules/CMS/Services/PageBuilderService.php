<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\PersistentCollection;
use Oforge\Engine\Modules\CMS\Abstracts\AbstractContentType;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\Models\ContentTypes\Row;

class PageBuilderService extends AbstractDatabaseAccess
{
    public function __construct()
    {
        parent::__construct(['pageContent' => PageContent::class, 'page' => Page::class]);
    }
    
    /**
     * Return page entity for given page id
     * @param int $pathId
     *
     * @return PageContent[]|NULL
     */
    private function getPageContentEntities(int $pathId)
    {
        /** @var PageContent[] $pageContentEntities */
        $pageContentEntities = $this->repository('pageContent')->findBy(["pagePath" => $pathId], ["order" => "ASC"]);
        
        if (isset($pageContentEntities)) {
            return $pageContentEntities;
        }
        return NULL;
    }
    
    /**
     * Return page entity for given page id
     * @param int $id
     * 
     * @return Page|NULL
     */
    private function getPageEntity(int $id)
    {
        /** @var Page $pageEntity */
        $pageEntity = $this->repository('page')->findOneBy(["id" => $id]);
        
        if (isset($pageEntity)) {
            return $pageEntity;
        }
        return NULL;
    }
    
    /**
     * Returns the content type group found as an associative array
     *
     * @param ContentTypeGroup|NULL $contentTypeGroupEntity
     *
     * @return array|NULL Array filled with available content type group data
     */
    private function getContentTypeGroupArray(?ContentTypeGroup $contentTypeGroupEntity)
    {
        if (!$contentTypeGroupEntity)
        {
            return NULL;
        }
        
        $contentTypeGroup                 = [];
        $contentTypeGroup["id"]           = $contentTypeGroupEntity->getId();
        $contentTypeGroup["name"]         = $contentTypeGroupEntity->getName();
        $contentTypeGroup["description"]  = $contentTypeGroupEntity->getDescription();
        
        return $contentTypeGroup;
    }
    
    /**
     * Returns the content type found as an associative array
     * @param ContentType $contentTypeEntity
     *
     * @return array|NULL Array filled with available content type data
     */
    private function getContentTypeArray(?ContentType $contentTypeEntity)
    {
        if (!$contentTypeEntity)
        {
            return NULL;
        }
        
        $contentType                = [];
        $contentType["id"]          = $contentTypeEntity->getId();
        $contentType["group"]       = $this->getContentTypeGroupArray($contentTypeEntity->getGroup());
        $contentType["name"]        = $contentTypeEntity->getName();
        $contentType["path"]        = $contentTypeEntity->getPath();
        $contentType["icon"]        = $contentTypeEntity->getIcon();
        $contentType["description"] = $contentTypeEntity->getDescription();
        $contentType["classPath"]   = $contentTypeEntity->getClassPath();
        
        return $contentType;
    }
    
    /**
     * Returns the content found as an associative array
     * @param Content $contentEntity
     *
     * @return array|NULL Array filled with available content data
     */
    private function getContentArray(?Content $contentEntity)
    {
        if (!$contentEntity)
        {
            return NULL;
        }
        
        $content              = [];
        $content["id"]        = $contentEntity->getId();
        $content["type"]      = $this->getContentTypeArray($contentEntity->getType());
        $content["parent"]    = $contentEntity->getParent();
        $content["name"]      = $contentEntity->getName();
        $content["cssClass"]  = $contentEntity->getCssClass();
        $content["data"]      = $contentEntity->getData();
        
        return $content;
    }
    
    /**
     * Returns the child data found as an associative array
     * @param array $childDatas filled with child data as defined in AbstractContentType
     * @see AbstractContentType::getChildData()
     *
     * @return array|NULL Array filled with available contents to fill child content elements
     */
    private function getChildContentDataArray($childDatas)
    {
        $childContents = [];
        foreach($childDatas as $childData)
        {
            $childContent            = [];
            $childContent["id"]      = $childData["id"];
            $childContent["content"] = $this->getContentArray($childData["content"]);
            $childContent["order"]   = $childData["order"];
            
            $childContents[] = $childContent;
        }
        
        return $childContents;
    }
    
    /**
     * Returns all found page contents as an associative array
     * @param int $pathId
     *
     * @return array|NULL Array filled with available page contents
     */
    private function getPageContentArray(int $pathId)
    {
        $pageContentEntities = $this->getPageContentEntities($pathId);
        
        if (!$pageContentEntities)
        {
            return NULL;
        }
        
        $pageContents = [];
        foreach($pageContentEntities as $pageContentEntity)
        {
            $pageContent            = [];
            $pageContent["id"]      = $pageContentEntity->getId();
            $pageContent["content"] = $this->getContentArray($pageContentEntity->getContent());
            $pageContent["order"]   = $pageContentEntity->getOrder();
            
            $pageContents[] = $pageContent;
        }
        
        return $pageContents;
    }
    
    /**
     * Returns the language found as an associative array
     * @param Language $languageEntity
     *
     * @return array|NULL Array filled with available language data
     */
    private function getLanguageArray(?Language $languageEntity)
    {
        if (!$languageEntity)
        {
            return NULL;
        }
        
        $language           = [];
        $language["id"]     = $languageEntity->getId();
        $language["iso"]    = $languageEntity->getIso();
        $language["name"]   = $languageEntity->getName();
        $language["active"] = $languageEntity->isActive();
        
        return $language;
    }
    
     /**
     * Returns all found page paths as an associative array
     * @param PersistentCollection|PagePath[]|NULL $pathEntities
     *
     * @return array|NULL Array filled with available paths
     */
    private function getPathArray(?PersistentCollection $pathEntities)
    {
        if (!$pathEntities)
        {
            return NULL;
        }
        
        $paths = [];
        foreach($pathEntities as $pathEntity)
        {
            $path                 = [];
            $path["id"]           = $pathEntity->getId();
            $path["language"]     = $this->getLanguageArray($pathEntity->getLanguage());
            $path["path"]         = $pathEntity->getPath();
            $path["pageContent"]  = $this->getPageContentArray($path["id"]);
            
            $paths[$path["language"]["id"]] = $path;
        }
        
        return $paths;
    }

    /**
     * Returns the page found as an associative array
     * @param int $id
     *
     * @return array|NULL Array filled with available page data
     */
    public function getPageArray(int $id)
    {
        $pageEntity = $this->getPageEntity($id);
        
        if (!$pageEntity)
        {
            return NULL;
        }
        
        $page = [];
        $page["id"]     = $pageEntity->getId();
        $page["layout"] = $pageEntity->getLayout();
        $page["site"]   = $pageEntity->getSite();
        $page["name"]   = $pageEntity->getName();
        $page["parent"] = $pageEntity->getParent();
        $page["paths"]  = $this->getPathArray($pageEntity->getPaths());
        
        return $page;
    }
    
    /**
     * Returns the current element id based on element hierachy and own id
     * @param string element id
     * @param int content id
     *
     * @return string element id
     */
    private function createCurrentElementId(string $elementId, int $contentId)
    {
        return $elementId . (!empty($elementId) ? '-' : '') . $contentId;
    }
    
    /**
     * Creates and returns an array with prepared twig content data for page builder
     * @param string element id to search for
     * @param string element id for history level data (internal use only)
     *
     * @return array|NULL Array filled with twig content data for page builder
     */
    private function createContentDataArray(?array $pageContent, string $elementId, string $_elementId)
    {
        if (!$pageContent)
        {
            return NULL;
        }
        
        $content = new $pageContent["content"]["type"]["classPath"];
        
        $content->load($pageContent["content"]["id"]);
        
        $data = [];
        $data["id"]    = $this->createCurrentElementId($_elementId, $pageContent["content"]["id"]);
        $data["se"]    = $elementId;
        $data["order"] = $pageContent["order"];
        
        $data = array_merge($data, $content->getRenderData());
        
        if ($content->isContainer())
        {
            $childDatas = $content->getChildData();
            
            if (is_array($childDatas))
            {
                $data["childs"]= $this->getContentDataArray($this->getChildContentDataArray($childDatas), $elementId, $data["id"]);
            }
        }
        
        return $data;
    }
    
    /**
     * Returns an array with prepared twig content data for page builder
     * @param array page contents array at base level
     * @param string element id to search for
     * @param string element id for history level data (internal use only)
     *
     * @return array|NULL Array filled with twig content data for page builder
     */
    public function getContentDataArray(?array $pageContents, string $elementId = '', string $_elementId = '')
    {
        if (!$pageContents)
        {
            return NULL;
        }
        
        $contents = [];
        foreach($pageContents as $pageContent)
        {
            $_contents = $this->createContentDataArray($pageContent, $elementId, $_elementId);
            if ($_contents === false)
            {
                continue;
            }
            
            $contents[] = $_contents;
        }
        
        return $contents;
    }
    
    /**
     * Returns an array with prepared twig content data for page builder by element id
     * @param array page contents array at base level
     * @param string element id to search for
     * @param string element id for history level data (internal use only)
     *
     * @return array|NULL Array filled with twig content data for page builder
     */
    public function getContentDataArrayById(?array $pageContents, string $elementId, string $_elementId = '')
    {
        if (!$pageContents)
        {
            return NULL;
        }
        
        foreach($pageContents as $pageContent)
        {
            // if element is found return content to display on page
            if ($pageContent["content"]["id"] > 0 && $this->createCurrentElementId($_elementId, $pageContent["content"]["id"]) === $elementId)
            {
                return $this->createContentDataArray($pageContent, $elementId, $_elementId);
            }
            
            // if element was not found but is a container type recursivly call getContentDataArrayById
            if ($pageContent["content"]["type"]["group"]["name"] == "container")
            {
                $content = new $pageContent["content"]["type"]["classPath"];
                
                $content->load($pageContent["content"]["id"]);
                
                if ($content->isContainer())
                {
                    $childDatas = $content->getChildData();
                    
                    if (is_array($childDatas))
                    {
                        return $this->getContentDataArrayById($this->getChildContentDataArray($childDatas), $elementId, $this->createCurrentElementId($_elementId, $pageContent["content"]["id"]));
                    }
                }
            }
        }
        
        return NULL;
   }
}