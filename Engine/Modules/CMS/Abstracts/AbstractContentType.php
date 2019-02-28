<?php

namespace Oforge\Engine\Modules\CMS\Abstracts;

use Oforge\Engine\Modules\Core\Abstracts\AbstractDatabaseAccess;
use Oforge\Engine\Modules\CMS\Models\Content\ContentType;
use Oforge\Engine\Modules\CMS\Models\Content\Content;

abstract class AbstractContentType extends AbstractDatabaseAccess
{
    protected $entityManager = Null;
    
    private $content         = Null;
    
    private $id              = Null;
    private $contentTypeId   = Null;
    private $parentId        = Null;
    private $name            = Null;
    private $cssClass        = Null;
    private $data            = Null;
    
    public function __construct()
    {
        parent::__construct(['contentType' => ContentType::class, 'content' => Content::class]);
        
        $contentTypeEntity   = $this->repository('contentType')->findOneBy(["classPath" => get_class($this)]);
        $this->contentTypeId = $contentTypeEntity->getId();
        
        $this->entityManager = Oforge()->DB()->getManager();
    }
    
    /**
     * Return whether or not content type is a container type like a row
     *
     * @return bool true|false
     */
    abstract public function isContainer(): bool;
    
    /**
     * Return data of content type
     *
     * @return mixed
     */
    abstract public function getData();
    
    /**
     * Set data of content type
     * @param mixed $data
     *
     * @return ContentType $this
     */
    abstract public function setData($data);
    
    /**
     * Return parent id of content type
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }
    
    /**
     * Set parent id content type
     * @param int $parentId
     *
     * @return ContentType $this
     */
    public function setParentId(int $parentId)
    {
        $this->parentId = $parentId;
        
        return $this;
    }
    
    /**
     * Return name of content type
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name content type
     * @param string $name
     *
     * @return ContentType $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    /**
     * Return css class of content type
     *
     * @return string
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }
    
    /**
     * Set css class content type
     * @param string $cssClass
     *
     * @return ContentType $this
     */
    public function setCssClass(string $cssClass)
    {
        $this->cssClass = $cssClass;
        
        return $this;
    }
    
    /**
     * Persist data stored in $content of content type to database
     *
     * @return ContentType $this
     */
    public function save() {
        if (!$this->id || !$this->content)
        {
            $this->content = new Content;
        }
        else
        {
            $this->content->setId($this->id);
        }
        
        $this->content->setType($this->contentTypeId);
        $this->content->setParent($this->parentId);
        $this->content->setName($this->name);
        $this->content->setCssClass($this->cssClass);
        $this->content->setData(serialize($this->data));
        
        $this->entityManager->persist($this->content);
        $this->entityManager->flush();
        
        if (!$this->id)
        {
            $this->id = $this->entityManager->getId();
            $this->content->setId($this->id);
        }
        
        return $this;
    }
    
    /**
     * Load data of content type from database to $content
     *
     * @return ContentType $this
     */
    public function load(int $id) {
        if (!$id)
        {
            return $this;
        }
        
        $content = $this->contentRepository->findOneBy(["id" => $id]);
        
        if ($content && $content->getId() > 0 && $content->getType() == $this->contentTypeId)
        {
            $this->id            = $content->getId();
            $this->parentId      = $content->getParent();
            $this->name          = $content->getName();
            $this->cssClass      = $content->getCssClass();
            $this->data          = unserialize($content->getData());
        }
        else
        {
            $this->content       = Null;
            
            $this->id            = Null;
            $this->parentId      = Null;
            $this->name          = Null;
            $this->cssClass      = Null;
            $this->data          = Null;
        }
        
        return $this;
    }
}