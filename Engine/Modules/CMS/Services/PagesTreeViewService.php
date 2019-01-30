<?php

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\Page;

class PagesTreeViewService
{
    private $entityManager;
    private $repository;

    public function __construct()
    {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(Page::class);
    }
    
    /**
     * Gets all pages from database and stores the
     * page object entity array in instance variable $pages
     */
    public function getPagesFromDB()
    {
        return $this->repository->findAll();
    }
    
    /**
     * Returns available page entities
     * 
     * @return Page[]|NULL
     */
    public function getPageEntities()
    {
        $pageEntityArray = $this->getPagesFromDB();
        
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
     * @return array Array filled with available pages
     */
    public function getPageArray()
    {
        $pageEntities = $this->getPageEntities();
        
        $pages = [];
        foreach($pageEntities as $pageEntity)
        {
            $page = [];
            $page["id"] = $pageEntity->getId();
            $page["name"] = $pageEntity->getName();
            $page["parent"] = $pageEntity->getParent();
            
            $pages[] = $page;
        }
        
        return $pages;
    }
    
    /**
     * Generate a jsTree configuration file with page data included
     * 
     * @return array jsTree configuration file as PHP array
     */
    public function generateJsTreeConfigJSON()
    {
        $pages = $this->getPageArray();
        
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
                "themes" => ["stripes" => TRUE],
                "data" => $jsTreePageArray
            ]
        ];
        
        return $jsTreeJSON;
    }
}