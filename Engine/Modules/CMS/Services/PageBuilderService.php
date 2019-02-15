<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\PersistentCollection;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

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
        $pageContentEntities = $this->repository('pageContent')->findBy(["pagePath" => $pathId]);
        
        if (isset($pageContentEntities)) {
            return $pageContentEntities;
        }
        return null;
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
        return null;
    }

    /**
     * Returns the content type group found as an associative array
     *
     * @param ContentTypeGroup|null $contentTypeGroupEntity
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
     * @param PersistentCollection|PagePath[]|null $pathEntities
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
            
            $paths[] = $path;
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
     * Returns an array with prepared twig content data for page builder
     * @param array page array
     * @param int page path
     *
     * @return array|NULL Array filled with twig content data for page builder
     */
    public function getContentDataArray(array $pageArray, $pagePath)
    {
        $pageContents = $pageArray["paths"][$pagePath]["pageContent"];
        
        if (!$pageContents)
        {
            return NULL;
        }
        
        $contents = [];
        foreach($pageContents as $pageContent)
        {
            $data = [];
            // TODO: set or choose correct language
            switch($pageContent["content"]["type"]["name"])
            {
                case "row":
                    $data["type"]   = "ContentTypes/Row/PageBuilder.twig";
                    // TODO: implement row data collection
                    break;
                case "richtext":
                    $data["type"]   = "ContentTypes/RichText/PageBuilder.twig";
                    $data["css"]    = $pageContent["content"]["cssClass"];
                    $data["text"]   = $pageContent["content"]["data"];
                    break;
                case "image":
                    $data["type"]   = "ContentTypes/Image/PageBuilder.twig";
                    $data["css"]    = $pageContent["content"]["cssClass"];
                    $data["url"]    = "/Tests/dummy_media/" . $pageContent["content"]["data"];
                    $data["alt"]    = $pageContent["content"]["name"];
                    break;
                default:
                    continue 2;
            }
            
            $contents[] = $data;
        }
        
        return $contents;
    }
}