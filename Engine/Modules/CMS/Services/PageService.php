<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 17.01.2019
 * Time: 10:52
 */

namespace Oforge\Engine\Modules\CMS\Services;

use Oforge\Engine\Modules\CMS\Models\Page\PagePath;


class PageService {
    
    private $entityManager;
    private $repository;
    
    public function __construct() {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(PagePath::class);
    }
    // prüfe, ob pfad vorhanden
    // ja > return
    // nein > false
    
    /**
     * Check if there is a cms url path
     * @param string $path
     *
     * @return bool
     */
    public function hasPath(string $path) {
        $data =  $this->repository->findOneBy(["path" => $path]) ;
        return isset($data);
    }
    
    /**
     * @param string $path
     *
     * @return PagePath|null
     */
    public function getPage(string $path): ?PagePath {
       return $this->repository->findOneBy(["path" => $path]);
    }
}