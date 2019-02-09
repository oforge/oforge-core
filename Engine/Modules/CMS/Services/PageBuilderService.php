<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\Page;

class PageBuilderService
{
    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(Page::class);
    }
    
    /**
     * Returns all available page entities
     * 
     * @return Page[]|NULL
     */
    public function getPageEntities()
    {
        $pageEntityArray = $this->repository->findAll();
        
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
     * @return array|NULL Array filled with available pages
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
            $page["name"] = $pageEntity->getName();
            $page["parent"] = $pageEntity->getParent();
            
            $pathEntities = $pageEntity->getPaths();
            
            $pathArray = [];
            foreach($pathEntities as $pathEntity)
            {
                $pathArray[] = $pathEntity->getPath();
            }
            
            $page["paths"] = $pathArray;
            
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
        
        $jsTreePageArray = [];
        foreach ($pages as $page)
        {
            $jsTreePageArray[] = [
                "id" => $page["id"],
                "parent" => $page["parent"] ? $page["parent"] : "#",
                "text" => $page["name"]
            ];
        }
        
        $jsTreeJSON = [
            "core" => [
                "animation" => 0,
                "check_callback" => TRUE,
                "force_text" => TRUE,
                "themes" => ["stripes" => FALSE],
                "data" => $jsTreePageArray
            ]
        ];
        
        return $jsTreeJSON;
    }
}