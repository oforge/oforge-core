<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Doctrine\ORM\PersistentCollection;
use Oforge\Engine\Modules\I18n\Models\Language;
use Oforge\Engine\Modules\I18n\Models\Snippet;
use Oforge\Engine\Modules\CMS\Models\Layout\Layout;
use Oforge\Engine\Modules\CMS\Models\Layout\Slot;
use Oforge\Engine\Modules\CMS\Models\Site\Site;
use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\CMS\Models\Page\PageContent;
use Oforge\Engine\Modules\CMS\Models\Content\ContentTypeGroup;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;
use Oforge\Engine\Modules\CMS\ContentTypes\Row;
use Oforge\Engine\Modules\CMS\ContentTypes\RichText;
use Oforge\Engine\Modules\CMS\ContentTypes\Image;

class PageBuilderService
{
    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->pageContentRepository = $this->entityManager->getRepository(PageContent::class);
        $this->pageRepository = $this->entityManager->getRepository(Page::class);
    }
    
    /**
     * Return page entity for given page id
     * @param int $pathId
     *
     * @return Page|NULL
     */
    private function getPageContentEntities(int $pathId)
    {
        $pageContentEntities = $this->pageContentRepository->findBy(["pagePath" => $pathId]);
        
        if (isset($pageContentEntities))
        {
            return $pageContentEntities;
        }
        else
        {
            return NULL;
        }
    }
    
        /**
     * Return page entity for given page id
     * @param int $id
     * 
     * @return Page|NULL
     */
    private function getPageEntity(int $id)
    {
        $pageEntity = $this->pageRepository->findOneBy(["id" => $id]);
        
        if (isset($pageEntity))
        {
            return $pageEntity;
        }
        else
        {
            return NULL;
        }
    }
    
    /**
     * Returns the content type group found as an associative array
     * @param ContentTypeGroup $contentTypeGroup
     *
     * @return array|NULL Array filled with available content type group data
     */
    private function getContentTypeGroupArray(?ContentTypeGroup $contentTypeGroupEntity)
    {
        if (!$contentTypeGroupEntity)
        {
            return NULL;
        }
        
        $contentTypeGroup = [];
        $contentTypeGroup["id"] = $contentTypeGroupEntity->getId();
        $contentTypeGroup["name"] = $contentTypeGroupEntity->getName();
        $contentTypeGroup["description"] = $contentTypeGroupEntity->getDescription();
        
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
        
        $contentType = [];
        $contentType["id"] = $contentTypeEntity->getId();
        $contentType["group"] = $this->getContentTypeGroupArray($contentTypeEntity->getGroup());
        $contentType["name"] = $contentTypeEntity->getName();
        $contentType["icon"] = $contentTypeEntity->getIcon();
        $contentType["description"] = $contentTypeEntity->getDescription();
        $contentType["classPath"] = $contentTypeEntity->getClassPath();
        
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
        
        $content = [];
        $content["id"] = $contentEntity->getId();
        $content["type"] = $this->getContentTypeArray($contentEntity->getType());
        $content["parent"] = $contentEntity->getParent();
        $content["name"] = $contentEntity->getName();
        $content["cssClass"] = $contentEntity->getCssClass();
        $content["data"] = $contentEntity->getData();
        
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
            $pageContent = [];
            $pageContent["id"] = $pageContentEntity->getId();
            $pageContent["content"] = $this->getContentArray($pageContentEntity->getContent());
            $pageContent["order"] = $pageContentEntity->getOrder();
            
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
        
        $language = [];
        $language["id"] = $languageEntity->getId();
        $language["iso"] = $languageEntity->getIso();
        $language["name"] = $languageEntity->getName();
        $language["active"] = $languageEntity->isActive();
        
        return $language;
    }
    
     /**
     * Returns all found page paths as an associative array
     * @param PersistentCollection $pathEntities
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
            $path = [];
            $path["id"] = $pathEntity->getId();
            $path["language"] = $this->getLanguageArray($pathEntity->getLanguage());
            $path["path"] = $pathEntity->getPath();
            $path["pageContent"] = $this->getPageContentArray($path["id"]);
            
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
        $page["id"] = $pageEntity->getId();
        $page["layout"] = $pageEntity->getLayout();
        $page["site"] = $pageEntity->getSite();
        $page["name"] = $pageEntity->getName();
        $page["parent"] = $pageEntity->getParent();
        $page["paths"] = $this->getPathArray($pageEntity->getPaths());
        
        return $page;
    }
}