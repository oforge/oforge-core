<?php
/**
 * Created by PhpStorm.
 * User: Alexander Wegner
 * Date: 16.01.2019
 * Time: 11:45
 */

namespace Oforge\Engine\Modules\CMS\Abstracts;

use Oforge\Engine\Modules\CMS\Models\Content\Content;

abstract class AbstractContentType {
    protected $entityManager;
    protected $repository;
    public function __construct() {
        $this->entityManager = Oforge()->DB()->getManager();
        $this->repository = $this->entityManager->getRepository(Content::class);
    }
    
    public function save(array $params) {
        $content = null;
        $id = isset($params["id"]) ? $params["id"] : null;
        $data = isset($params["data"]) ? $params["data"] : null;
        $name = isset($params["name"]) ? $params["name"] : null;
        $type = isset($params["type"]) ? $params["type"] : null;
        
        // type
        // content
        // id
        // [id?, type=>richtext, data: <div></div>]
        //[id?, type=2colums, data: [{ id: , type, data }, {  id: type: data}]
        
        
        if ($id) {
            $content = $this->repository->find($params["id"]);
        } else {
            $content = new Content();
        }
    
        $content->setName($name);
        $content->setData($data);
        $content->setType($type);
        
        $this->entityManager->persist($content);
        $this->entityManager->flush($content);
    }
    
    /**
     * @param int $id
     *
     * @return object|Content|null
     */
    public function load(int $id) {
        return $this->repository->find($id);
    }
}