<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\CMS\Models\Page\PagePath;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\I18n\Models\Language;

class PageTreeService extends AbstractDatabaseAccess
{
    private $entityManager;
    private $repository;
    
    public function __construct()
    {
        parent::__construct(["default" => Page::class]);
    }
    
    /**
     * Returns all available page entities
     *
     * @return Page[]|NULL
     */
    private function getPageEntities()
    {
        $pageEntityArray = $this->repository()->findAll();
        
        if (isset($pageEntityArray))
        {
            return $pageEntityArray;
        }
        else
        {
            return NULL;
        }
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
     * Returns all found pages as an associative array
     *
     * @return array|NULL Array filled with available pages including path data
     */
    public function getPageArray()
    {
        $pageEntities = $this->getPageEntities();
        
        if (!$pageEntities)
        {
            return NULL;
        }
        
        $pages = [];
        foreach($pageEntities as $pageEntity)
        {
            $page = [];
            $page["id"]     = $pageEntity->getId();
            $page["layout"] = $pageEntity->getLayout();
            $page["site"]   = $pageEntity->getSite();
            $page["name"]   = $pageEntity->getName();
            $page["parent"] = $pageEntity->getParent();

            /** @var PagePath[] $pathEntities */
            $pathEntities = $pageEntity->getPaths();
            // var_dump($pathEntities);
            $paths = [];
            if (!empty($pathEntities)) {
                foreach ($pathEntities as $pathEntity) {
                    $path             = [];
                    $path["id"]       = $pathEntity->getId();
                    $path["language"] = $this->getLanguageArray($pathEntity->getLanguage());
                    $path["path"]     = $pathEntity->getPath();

                    $paths[$path["language"]["id"]] = $path;
                }
            }

            $page["paths"] = $paths;
            
            $pages[] = $page;
        }
        
        return $pages;
    }
    
    /**
     * Generate a jsTree configuration file with page data included
     *
     * @return array|NULL jsTree configuration file as PHP array
     */
    public function generateJsTreeConfigJSON()
    {
        $pages = $this->getPageArray();
        
        if (!$pages)
        {
            return NULL;
        }
        
        $jsTreePages = [];
        foreach ($pages as $page)
        {
            $jsTreePages[] = [
                "id"     => $page["id"],
                "icon"   => "jstree-file",
                "parent" => $page["parent"] ? $page["parent"] : "#",
                "text"   => $page["name"]
            ];
        }
        
        return $jsTreePages;
    }
}
