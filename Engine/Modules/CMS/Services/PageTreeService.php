<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\Page;
use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;

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
            $page["id"] = $pageEntity->getId();
            $page["layout"] = $pageEntity->getLayout();
            $page["site"] = $pageEntity->getSite();
            $page["name"] = $pageEntity->getName();
            $page["parent"] = $pageEntity->getParent();
            
            $pathEntities = $pageEntity->getPaths();
            
            $paths = [];
            foreach($pathEntities as $pathEntity)
            {
                $path = [];
                $path["id"] = $pathEntity->getId();
                // TODO: decode Language-Entity to Array
                $path["language"] = $pathEntity->getLanguage();
                $path["path"] = $pathEntity->getPath();
                
                $paths[] = $path;
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
                "id" => $page["id"],
                "parent" => $page["parent"] ? $page["parent"] : "#",
                "text" => $page["name"]
            ];
        }
        
        $jsTreeJSON = [
            "core" => [
                "multiple" => FALSE,
                "animation" => 0,
                "check_callback" => TRUE,
                "force_text" => TRUE,
                "themes" => ["stripes" => FALSE],
                "data" => $jsTreePages
            ]
        ];
        
        return $jsTreeJSON;
    }
}